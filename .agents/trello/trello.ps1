param(
    [Parameter(Mandatory = $true, Position = 0)]
    [ValidateSet("test", "boards", "lists", "cards", "card", "create-card", "comment", "move-card", "attach-url")]
    [string]$Command,

    [Parameter(Mandatory = $false)]
    [string]$BoardId,

    [Parameter(Mandatory = $false)]
    [string]$ListId,

    [Parameter(Mandatory = $false)]
    [string]$CardId,

    [Parameter(Mandatory = $false)]
    [int]$CardNumber,

    [Parameter(Mandatory = $false)]
    [string]$Name,

    [Parameter(Mandatory = $false)]
    [string]$Desc,

    [Parameter(Mandatory = $false)]
    [string]$Text,

    [Parameter(Mandatory = $false)]
    [string]$Url,

    [Parameter(Mandatory = $false)]
    [ValidateSet("top", "bottom")]
    [string]$Position = "top"
)

$ErrorActionPreference = "Stop"

function Get-ScriptDir {
    return Split-Path -Parent $PSCommandPath
}

function Read-TrelloConfig {
    $configPath = Join-Path (Get-ScriptDir) "config.local.json"
    $config = [pscustomobject]@{}

    if (Test-Path -LiteralPath $configPath) {
        $config = Get-Content -Raw -LiteralPath $configPath | ConvertFrom-Json
    }

    $key = if ($config.key) { $config.key } else { $env:TRELLO_KEY }
    $token = if ($config.token) { $config.token } else { $env:TRELLO_TOKEN }
    $boardId = if ($BoardId) { $BoardId } elseif ($config.boardId) { $config.boardId } else { $env:TRELLO_BOARD_ID }

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
        [hashtable]$Body = @{}
    )

    $config = Read-TrelloConfig
    $auth = New-TrelloAuthQuery -Config $config
    $separator = if ($Path.Contains("?")) { "&" } else { "?" }
    $url = "https://api.trello.com/1/$Path$separator$auth"

    if ($Body.Count -gt 0) {
        return Invoke-RestMethod -Method $Method -Uri $url -Body $Body
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

    "lists" {
        $config = Read-TrelloConfig

        if ([string]::IsNullOrWhiteSpace($config.BoardId)) {
            throw "BoardId não informado. Use -BoardId, config.local.json ou TRELLO_BOARD_ID."
        }

        $result = Invoke-TrelloRequest -Method "GET" -Path "boards/$($config.BoardId)/lists?fields=id,name,closed,pos"
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

    "card" {
        $config = Read-TrelloConfig

        if ([string]::IsNullOrWhiteSpace($CardId) -and $CardNumber -le 0) {
            throw "Informe CardId ou CardNumber para consultar um card."
        }

        if ($CardNumber -gt 0 -and [string]::IsNullOrWhiteSpace($config.BoardId)) {
            throw "BoardId é obrigatório para consultar card por número. Use -BoardId, config.local.json ou TRELLO_BOARD_ID."
        }

        if ($CardNumber -gt 0) {
            $cards = Invoke-TrelloRequest -Method "GET" -Path "boards/$($config.BoardId)/cards?fields=id,idShort,name,desc,closed,url,idList,pos"
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

        $body = @{
            idList = $ListId
            name = $Name
        }

        if (![string]::IsNullOrWhiteSpace($Desc)) {
            $body.desc = $Desc
        }

        $result = Invoke-TrelloRequest -Method "POST" -Path "cards" -Body $body
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

        $result = Invoke-TrelloRequest -Method "POST" -Path "cards/$CardId/actions/comments" -Body @{ text = $Text }
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
            $body.name = $Name
        }

        $result = Invoke-TrelloRequest -Method "POST" -Path "cards/$CardId/attachments" -Body $body
        Write-Json $result
        break
    }
}
