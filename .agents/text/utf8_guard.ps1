function Read-ExternalUtf8Text {
    param([Parameter(Mandatory = $true)][string]$Path)

    $utf8 = [Text.UTF8Encoding]::new($false, $true)
    try {
        return [IO.File]::ReadAllText($Path, $utf8)
    } catch [Text.DecoderFallbackException] {
        $windows1252 = [Text.Encoding]::GetEncoding(
            1252,
            [Text.EncoderExceptionFallback]::new(),
            [Text.DecoderExceptionFallback]::new()
        )
        return [IO.File]::ReadAllText($Path, $windows1252)
    }
}

function Protect-ExternalText {
    param(
        [Parameter(Mandatory = $true)][AllowEmptyString()][string]$Text,
        [Parameter(Mandatory = $true)][string]$FieldName
    )

    $guard = Join-Path $PSScriptRoot "utf8_guard.php"
    $source = [IO.Path]::GetTempFileName()
    $target = [IO.Path]::GetTempFileName()

    try {
        [IO.File]::WriteAllText($source, $Text, [Text.UTF8Encoding]::new($false))
        & php $guard normalize-file $source $target $FieldName
        if ($LASTEXITCODE -ne 0) {
            throw "$FieldName não passou pela proteção UTF-8 e antimojibake."
        }
        return [IO.File]::ReadAllText($target, [Text.UTF8Encoding]::new($false, $true))
    } finally {
        Remove-Item -LiteralPath $source, $target -Force -ErrorAction SilentlyContinue
    }
}

function Protect-ExternalPayload {
    param(
        [Parameter(Mandatory = $false)]$Value,
        [string]$FieldPath = "payload"
    )

    if ($null -eq $Value) {
        return $null
    }

    if ($Value -is [string]) {
        return Protect-ExternalText -Text $Value -FieldName $FieldPath
    }

    if ($Value -is [Collections.IDictionary]) {
        $protected = [ordered]@{}
        foreach ($key in $Value.Keys) {
            $protected[$key] = Protect-ExternalPayload -Value $Value[$key] -FieldPath "$FieldPath.$key"
        }
        return $protected
    }

    if ($Value -is [Collections.IEnumerable] -and $Value -isnot [string]) {
        return @(
            $index = 0
            foreach ($item in $Value) {
                Protect-ExternalPayload -Value $item -FieldPath "$FieldPath[$index]"
                $index++
            }
        )
    }

    if ($Value.GetType().FullName -eq 'System.Management.Automation.PSCustomObject') {
        $protected = [ordered]@{}
        foreach ($property in $Value.PSObject.Properties) {
            $protected[$property.Name] = Protect-ExternalPayload -Value $property.Value -FieldPath "$FieldPath.$($property.Name)"
        }
        return [pscustomobject]$protected
    }

    return $Value
}
