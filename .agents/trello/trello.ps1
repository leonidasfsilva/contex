param(
    [Parameter(Mandatory = $true, Position = 0)]
    [ValidateSet("test", "boards", "create-board", "lists", "cards", "all-cards", "card", "create-card", "update-card", "checklists", "create-checklist", "add-checkitem", "checkitem", "rename-checkitem", "comment", "move-card", "attach-url")]
    [string]$Command,

    [Parameter(Mandatory = $false)]
    [string]$BoardId,

    [Parameter(Mandatory = $false)]
    [string]$ListId,

    [Parameter(Mandatory = $false)]
    [string]$CardId,

    [Parameter(Mandatory = $false)]
    [string]$CheckItemId,

    [Parameter(Mandatory = $false)]
    [string]$ChecklistId,

    [Parameter(Mandatory = $false)]
    [string]$ChecklistName,

    [Parameter(Mandatory = $false)]
    [int]$CardNumber,

    [Parameter(Mandatory = $false)]
    [string]$Name,

    [Parameter(Mandatory = $false)]
    [string]$Desc,

    [Parameter(Mandatory = $false)]
    [string]$DescFile,

    [Parameter(Mandatory = $false)]
    [switch]$ClearDesc,

    [Parameter(Mandatory = $false)]
    [string]$Text,

    [Parameter(Mandatory = $false)]
    [string]$Url,

    [Parameter(Mandatory = $false)]
    [ValidateSet("top", "bottom")]
    [string]$Position = "top"

    ,
    [Parameter(Mandatory = $false)]
    [ValidateSet("complete", "incomplete")]
    [string]$State
)

$ErrorActionPreference = "Stop"
$guardScript = Join-Path (Split-Path -Parent $PSCommandPath) "..\text\utf8_guard.ps1"
. $guardScript

if ($Host.Name -eq "ConsoleHost") {
    [Console]::InputEncoding = [Text.UTF8Encoding]::new($false)
    [Console]::OutputEncoding = [Text.UTF8Encoding]::new($false)
}

function Get-ScriptDir {
    return Split-Path -Parent $PSCommandPath
}

function Get-ProjectKey {
    if ($env:TRELLO_PROJECT) {
        return $env:TRELLO_PROJECT
    }

    $currentPath = (Get-Location).Path.TrimEnd([char[]]@([char]92, [char]47))
    $projectName = Split-Path -Leaf $currentPath

    if ($projectName -in @('contex', 'contex-spa')) {
        return $projectName
    }

    return $null
}

function Read-TrelloConfig {
    $configPath = Join-Path (Get-ScriptDir) "config.local.json"
    $config = [pscustomobject]@{}

    if (Test-Path -LiteralPath $configPath) {
        $config = Read-ExternalUtf8Text -Path $configPath | ConvertFrom-Json
    }

    $key = if ($config.key) { $config.key } else { $env:TRELLO_KEY }
    $token = if ($config.token) { $config.token } else { $env:TRELLO_TOKEN }
    $projectKey = Get-ProjectKey
    $configuredBoardId = if ($projectKey -and $config.boardIds) {
        $config.boardIds.$projectKey
    }
    $boardId = if ($BoardId) {
        $BoardId
    } elseif ($env:TRELLO_BOARD_ID) {
        $env:TRELLO_BOARD_ID
    } elseif ($configuredBoardId) {
        $configuredBoardId
    } else {
        $config.boardId
    }

    if ([string]::IsNullOrWhiteSpace($key)) {
        throw "TRELLO_KEY não configurada. Preencha .agents/trello/config.local.json ou a variável de ambiente TRELLO_KEY."
    }

    if ([string]::IsNullOrWhiteSpace($token)) {
        throw "TRELLO_TOKEN não configurado. Preencha .agents/trello/config.local.json ou a variável de ambiente TRELLO_TOKEN."
    }

    return [pscustomobject]@{
        Key = $key
        Token = $token
        BoardId = $boardId
        Project = $projectKey
    }
}

function New-TrelloAuthQuery {
    param([object]$Config)

    return "key=$([uri]::EscapeDataString($Config.Key))&token=$([uri]::EscapeDataString($Config.Token))"
}

function Invoke-TrelloRequest {
    param(
        [Parameter(Mandatory = $true)]
        [ValidateSet("GET", "POST", "PUT", "DELETE")]
        [string]$Method,

        [Parameter(Mandatory = $true)]
        [string]$Path,

        [Parameter(Mandatory = $false)]
        [object]$Body = $null
    )

    $config = Read-TrelloConfig
    $auth = New-TrelloAuthQuery -Config $config
    $separator = if ($Path.Contains("?")) { "&" } else { "?" }
    $url = "https://api.trello.com/1/$Path$separator$auth"

    if ($null -ne $Body) {
        $protectedBody = Protect-ExternalPayload -Value $Body -FieldPath "Trello"
        $json = $protectedBody | ConvertTo-Json -Depth 20 -Compress
        $bytes = [Text.Encoding]::UTF8.GetBytes($json)
        $result = Invoke-RestMethod -Method $Method -Uri $url -Body $bytes -ContentType "application/json; charset=utf-8"
        if ($Method -ne "GET") {
            Protect-ExternalPayload -Value $result -FieldPath "resposta publicada pelo Trello" | Out-Null
        }
        return $result
    }

    return Invoke-RestMethod -Method $Method -Uri $url
}

function Write-Json {
    param([object]$Data)

    $Data | ConvertTo-Json -Depth 20
}

switch ($Command) {
    "test" {
        $result = Invoke-TrelloRequest -Method "GET" -Path "members/me?fields=id,username,fullName"
        Write-Json $result
        break
    }

    "boards" {
        $result = Invoke-TrelloRequest -Method "GET" -Path "members/me/boards?fields=id,name,closed,url"
        Write-Json $result
        break
    }

    "create-board" {
        if ([string]::IsNullOrWhiteSpace($Name)) {
            throw "Name e obrigatorio para criar board."
        }

        $Name = Protect-ExternalText -Text $Name -FieldName "Name"

        $body = @{
            name = $Name
        }

        if (![string]::IsNullOrWhiteSpace($Desc)) {
            $body.desc = Protect-ExternalText -Text $Desc -FieldName "Desc"
        }

        $result = Invoke-TrelloRequest -Method "POST" -Path "boards" -Body $body
        Write-Json $result
        break
    }

    "lists" {
        $config = Read-TrelloConfig

        $lookupBoardId = if ($BoardId) { $BoardId } else { $config.BoardId }

        if ([string]::IsNullOrWhiteSpace($lookupBoardId)) {
            throw "BoardId não informado. Use -BoardId, config.local.json ou TRELLO_BOARD_ID."
        }

        $result = Invoke-TrelloRequest -Method "GET" -Path "boards/$lookupBoardId/lists?fields=id,name,closed,pos"
        Write-Json $result
        break
    }

    "cards" {
        if ([string]::IsNullOrWhiteSpace($ListId)) {
            throw "ListId é obrigatório para listar cards."
        }

        $result = Invoke-TrelloRequest -Method "GET" -Path "lists/$ListId/cards?fields=id,name,desc,closed,url,idList,pos"
        Write-Json $result
        break
    }

    "all-cards" {
        $config = Read-TrelloConfig
        $lookupBoardId = if ($BoardId) { $BoardId } else { $config.BoardId }

        if ([string]::IsNullOrWhiteSpace($lookupBoardId)) {
            throw "BoardId não informado. Use -BoardId, config.local.json ou TRELLO_BOARD_ID."
        }

        $result = Invoke-TrelloRequest -Method "GET" -Path "boards/$lookupBoardId/cards/all?fields=id,idShort,name,desc,closed,url,idList,pos"
        Write-Json $result
        break
    }

    "card" {
        $config = Read-TrelloConfig

        if ([string]::IsNullOrWhiteSpace($CardId) -and $CardNumber -le 0) {
            throw "Informe CardId ou CardNumber para consultar um card."
        }

        if ($CardNumber -gt 0 -and [string]::IsNullOrWhiteSpace($BoardId) -and [string]::IsNullOrWhiteSpace($config.BoardId)) {
            throw "BoardId é obrigatório para consultar card por número. Use -BoardId, config.local.json ou TRELLO_BOARD_ID."
        }

        if ($CardNumber -gt 0) {
            $lookupBoardId = if ($BoardId) { $BoardId } else { $config.BoardId }
            $cards = Invoke-TrelloRequest -Method "GET" -Path "boards/$lookupBoardId/cards?fields=id,idShort,name,desc,closed,url,idList,pos"
            $result = $cards | Where-Object { $_.idShort -eq $CardNumber } | Select-Object -First 1

            if (!$result) {
                throw "Card número $CardNumber não encontrado no board configurado."
            }
        } else {
            $result = Invoke-TrelloRequest -Method "GET" -Path "cards/$CardId?fields=id,idShort,name,desc,closed,url,idList,pos"
        }

        Write-Json $result
        break
    }

    "create-card" {
        if ([string]::IsNullOrWhiteSpace($ListId)) {
            throw "ListId é obrigatório para criar card."
        }

        if ([string]::IsNullOrWhiteSpace($Name)) {
            throw "Name é obrigatório para criar card."
        }

        $Name = Protect-ExternalText -Text $Name -FieldName "Name"

        $body = @{
            idList = $ListId
            name = $Name
            pos = $Position
        }

        if (![string]::IsNullOrWhiteSpace($Desc)) {
            $body.desc = Protect-ExternalText -Text $Desc -FieldName "Desc"
        }

        $result = Invoke-TrelloRequest -Method "POST" -Path "cards" -Body $body
        Write-Json $result
        break
    }

    "update-card" {
        if ([string]::IsNullOrWhiteSpace($CardId)) {
            throw "CardId e obrigatorio para atualizar card."
        }

        $body = @{}

        if ($PSBoundParameters.ContainsKey("Name")) {
            if ([string]::IsNullOrWhiteSpace($Name)) {
                throw "Name nao pode ser vazio quando informado."
            }
            $body.name = Protect-ExternalText -Text $Name -FieldName "Name"
        }

        if ($PSBoundParameters.ContainsKey("Desc")) {
            $body.desc = Protect-ExternalText -Text $Desc -FieldName "Desc"
        }

        if ($PSBoundParameters.ContainsKey("DescFile")) {
            if (!(Test-Path -LiteralPath $DescFile)) {
                throw "DescFile nao encontrado."
            }
            $body.desc = Protect-ExternalText -Text (Read-ExternalUtf8Text -Path $DescFile) -FieldName "DescFile"
        }

        if ($ClearDesc) {
            $body.desc = ""
        }

        if ($body.Count -eq 0) {
            throw "Informe Name e/ou Desc para atualizar card."
        }

        $result = Invoke-TrelloRequest -Method "PUT" -Path "cards/$CardId" -Body $body

        if ($body.ContainsKey('desc') -and $result.desc -ne $body.desc) {
            throw "O Trello devolveu descrição diferente do texto sanitizado enviado."
        }

        Write-Json $result
        break
    }

    "comment" {
        if ([string]::IsNullOrWhiteSpace($CardId)) {
            throw "CardId é obrigatório para comentar."
        }

        if ([string]::IsNullOrWhiteSpace($Text)) {
            throw "Text é obrigatório para comentar."
        }

        $Text = Protect-ExternalText -Text $Text -FieldName "Text"

        $result = Invoke-TrelloRequest -Method "POST" -Path "cards/$CardId/actions/comments" -Body @{ text = $Text }
        if ($result.data.text -ne $Text -and $result.data.text) {
            throw "O Trello devolveu comentário diferente do texto sanitizado enviado."
        }
        Write-Json $result
        break
    }

    "checklists" {
        if ([string]::IsNullOrWhiteSpace($CardId)) {
            throw "CardId e obrigatorio para listar checklists."
        }

        $checklists = Invoke-TrelloRequest -Method "GET" -Path "cards/$CardId/checklists?fields=id,name,pos"
        $result = @(
            foreach ($checklist in @($checklists)) {
                $items = Invoke-TrelloRequest -Method "GET" -Path "checklists/$($checklist.id)/checkItems?fields=id,name,state,pos"
                [pscustomobject]@{
                    id = $checklist.id
                    name = $checklist.name
                    pos = $checklist.pos
                    checkItems = @($items)
                }
            }
        )

        Write-Json $result
        break
    }

    "create-checklist" {
        if ([string]::IsNullOrWhiteSpace($CardId)) {
            throw "CardId e obrigatorio para criar checklist."
        }

        if ([string]::IsNullOrWhiteSpace($ChecklistName)) {
            throw "ChecklistName e obrigatorio para criar checklist."
        }

        $ChecklistName = Protect-ExternalText -Text $ChecklistName -FieldName "ChecklistName"

        $body = @{ idCard = $CardId; name = $ChecklistName }
        $result = Invoke-TrelloRequest -Method "POST" -Path "checklists" -Body $body
        Write-Json $result
        break
    }

    "add-checkitem" {
        if ([string]::IsNullOrWhiteSpace($ChecklistId)) {
            throw "ChecklistId e obrigatorio para adicionar item da checklist."
        }

        if ([string]::IsNullOrWhiteSpace($Name)) {
            throw "Name e obrigatorio para adicionar item da checklist."
        }

        $Name = Protect-ExternalText -Text $Name -FieldName "Name"

        $body = @{ name = $Name }
        $result = Invoke-TrelloRequest -Method "POST" -Path "checklists/$ChecklistId/checkItems" -Body $body
        Write-Json $result
        break
    }

    "checkitem" {
        if ([string]::IsNullOrWhiteSpace($CardId)) {
            throw "CardId e obrigatorio para atualizar item da checklist."
        }

        if ([string]::IsNullOrWhiteSpace($ChecklistId)) {
            throw "ChecklistId e obrigatorio para atualizar item da checklist."
        }

        if ([string]::IsNullOrWhiteSpace($CheckItemId)) {
            throw "CheckItemId e obrigatorio para atualizar item da checklist."
        }

        if ([string]::IsNullOrWhiteSpace($State)) {
            throw "State e obrigatorio: complete ou incomplete."
        }

        $body = @{ state = $State }
        $result = Invoke-TrelloRequest -Method "PUT" -Path "cards/$CardId/checklist/$ChecklistId/checkItem/$CheckItemId" -Body $body
        Write-Json $result
        break
    }

    "rename-checkitem" {
        if ([string]::IsNullOrWhiteSpace($CardId)) {
            throw "CardId e obrigatorio para renomear item da checklist."
        }

        if ([string]::IsNullOrWhiteSpace($CheckItemId)) {
            throw "CheckItemId e obrigatorio para renomear item da checklist."
        }

        if ([string]::IsNullOrWhiteSpace($Name)) {
            throw "Name e obrigatorio para renomear item da checklist."
        }

        $Name = Protect-ExternalText -Text $Name -FieldName "Name"

        $body = @{ name = $Name }
        $result = Invoke-TrelloRequest -Method "PUT" -Path "cards/$CardId/checklist/$ChecklistId/checkItem/$CheckItemId" -Body $body
        Write-Json $result
        break
    }

    "move-card" {
        if ([string]::IsNullOrWhiteSpace($CardId)) {
            throw "CardId é obrigatório para mover card."
        }

        if ([string]::IsNullOrWhiteSpace($ListId)) {
            throw "ListId é obrigatório para mover card."
        }

        $list = [uri]::EscapeDataString($ListId)
        Invoke-TrelloRequest -Method "PUT" -Path "cards/$CardId/idList?value=$list" | Out-Null

        $pos = [uri]::EscapeDataString($Position)
        $result = Invoke-TrelloRequest -Method "PUT" -Path "cards/$CardId/pos?value=$pos"
        Write-Json $result
        break
    }

    "attach-url" {
        if ([string]::IsNullOrWhiteSpace($CardId)) {
            throw "CardId é obrigatório para anexar URL."
        }

        if ([string]::IsNullOrWhiteSpace($Url)) {
            throw "Url é obrigatória para anexar URL."
        }

        $body = @{
            url = $Url
        }

        if (![string]::IsNullOrWhiteSpace($Name)) {
            $body.name = Protect-ExternalText -Text $Name -FieldName "Name"
        }

        $result = Invoke-TrelloRequest -Method "POST" -Path "cards/$CardId/attachments" -Body $body
        Write-Json $result
        break
    }
}
