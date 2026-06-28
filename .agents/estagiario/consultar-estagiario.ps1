param(
    [Parameter(Mandatory = $false)]
    [string]$Prompt,

    [Parameter(Mandatory = $false)]
    [string[]]$ContextFiles = @(),

    [Parameter(Mandatory = $false)]
    [string]$Model = "qwen2.5-coder-14b-instruct",

    [Parameter(Mandatory = $false)]
    [string]$BaseUrl = "http://127.0.0.1:1234/v1",

    [Parameter(Mandatory = $false)]
    [int]$MaxTokens = 2500,

    [Parameter(Mandatory = $false)]
    [double]$Temperature = 0.2
,
    [Parameter(Mandatory = $false)]
    [switch]$IncludeAssistantRules
)

$ErrorActionPreference = "Stop"

function Get-RepoRoot {
    $scriptPath = Split-Path -Parent $PSCommandPath
    return (Resolve-Path (Join-Path $scriptPath "..\..")).Path
}

function Read-TextFileIfExists {
    param([string]$Path)

    if (Test-Path -LiteralPath $Path) {
        return Get-Content -Raw -LiteralPath $Path
    }

    return ""
}

function Read-ContextSpec {
    param(
        [string]$RepoRoot,
        [string]$Spec
    )

    $path = $Spec
    $range = $null

    if ($Spec.Contains("#")) {
        $parts = $Spec.Split("#", 2)
        $path = $parts[0]
        $range = $parts[1]
    }

    $fullPath = Join-Path $RepoRoot $path

    if (!(Test-Path -LiteralPath $fullPath)) {
        throw "Arquivo de contexto não encontrado: $path"
    }

    if (!$range) {
        return @(
            "ARQUIVO ${path}:",
            '```',
            (Get-Content -Raw -LiteralPath $fullPath),
            '```'
        ) -join "`n"
    }

    if ($range -notmatch "^(\d+)-(\d+)$") {
        throw "Intervalo inválido em '$Spec'. Use o formato arquivo.php#10-40."
    }

    $start = [int]$Matches[1]
    $end = [int]$Matches[2]
    $lines = Get-Content -LiteralPath $fullPath
    $selected = New-Object System.Collections.Generic.List[string]

    for ($i = $start; $i -le $end; $i++) {
        if ($i -le $lines.Count) {
            $selected.Add(("{0,4}: {1}" -f $i, $lines[$i - 1]))
        }
    }

    return @(
        "TRECHO ${Spec}:",
        '```',
        ($selected -join "`n"),
        '```'
    ) -join "`n"
}

if (!$Prompt) {
    $Prompt = [Console]::In.ReadToEnd()
}

if (!$Prompt.Trim()) {
    throw "Informe um prompt via -Prompt ou stdin."
}

$repoRoot = Get-RepoRoot
$rulesPath = Join-Path $repoRoot ".clinerules\estagiario.md"
$assistantRulesPath = Join-Path $repoRoot "docs\regras-assistente.md"

$rules = Read-TextFileIfExists $rulesPath
$assistantRules = ""

if ($IncludeAssistantRules) {
    $assistantRules = Read-TextFileIfExists $assistantRulesPath
}

$contextBlocks = New-Object System.Collections.Generic.List[string]
foreach ($spec in $ContextFiles) {
    $contextBlocks.Add((Read-ContextSpec -RepoRoot $repoRoot -Spec $spec))
}

$systemMessage = @"
Você é o estagiário local do projeto Contex.

REGRAS DO ESTAGIÁRIO:
$rules

REGRAS GERAIS DO PROJETO:
$assistantRules

Responda em português do Brasil, com grafia correta e acentuação.
Não aplique alterações diretamente no projeto.
Não execute comandos.
Não crie branches, commits ou PRs.
Entregue análise, rascunho, pseudo-patch ou proposta objetiva conforme solicitado.
"@

$userMessage = @"
TAREFA:
$Prompt

CONTEXTO:
$($contextBlocks -join "`n")
"@

$payload = [ordered]@{
    model = $Model
    messages = @(
        [ordered]@{ role = "system"; content = $systemMessage },
        [ordered]@{ role = "user"; content = $userMessage }
    )
    temperature = $Temperature
    max_tokens = $MaxTokens
}

$body = $payload | ConvertTo-Json -Depth 10 -Compress
$bytes = [System.Text.Encoding]::UTF8.GetBytes($body)
$url = "$BaseUrl/chat/completions"

$response = Invoke-RestMethod -Uri $url -Method Post -ContentType "application/json; charset=utf-8" -Body $bytes -TimeoutSec 180
$response.choices[0].message.content
