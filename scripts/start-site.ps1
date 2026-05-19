$ErrorActionPreference = "Stop"

Write-Host "Memastikan Docker Desktop berjalan..."
$dockerDesktop = Get-Process "Docker Desktop" -ErrorAction SilentlyContinue
if (-not $dockerDesktop) {
    $candidates = @(
        "$env:ProgramFiles\Docker\Docker\Docker Desktop.exe",
        "${env:ProgramFiles(x86)}\Docker\Docker\Docker Desktop.exe",
        "$env:LocalAppData\Docker\Docker Desktop.exe"
    )
    $dockerDesktopPath = $candidates | Where-Object { Test-Path -LiteralPath $_ } | Select-Object -First 1
    if (-not $dockerDesktopPath) {
        throw "Docker Desktop tidak ditemukan. Buka Docker Desktop secara manual, lalu jalankan ulang script ini."
    }

    Start-Process -FilePath $dockerDesktopPath -WindowStyle Hidden
    Start-Sleep -Seconds 12
}

Write-Host "Menunggu Docker engine siap..."
$dockerReady = $false
for ($i = 0; $i -lt 30; $i++) {
    try {
        docker info *> $null
        if ($LASTEXITCODE -eq 0) {
            $dockerReady = $true
            break
        }
    } catch {
        $dockerReady = $false
    }

    if ($dockerReady) {
        break
    }
    Start-Sleep -Seconds 2
}

if (-not $dockerReady) {
    throw "Docker engine belum siap. Buka Docker Desktop lalu jalankan ulang script ini."
}

docker compose up --build -d

Write-Host "Aplikasi berjalan:"
Write-Host "HTTPS: https://localhost:8443"
Write-Host "HTTP redirect: http://localhost:8080"
