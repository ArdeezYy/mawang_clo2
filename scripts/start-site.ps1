$ErrorActionPreference = "Stop"

Write-Host "Memastikan Docker Desktop berjalan..."
$dockerDesktop = Get-Process "Docker Desktop" -ErrorAction SilentlyContinue
if (-not $dockerDesktop) {
    Start-Process "Docker Desktop" -WindowStyle Hidden
    Start-Sleep -Seconds 8
}

Write-Host "Menunggu Docker engine siap..."
for ($i = 0; $i -lt 30; $i++) {
    docker info *> $null
    if ($LASTEXITCODE -eq 0) {
        break
    }
    Start-Sleep -Seconds 2
}

if ($LASTEXITCODE -ne 0) {
    throw "Docker engine belum siap. Buka Docker Desktop lalu jalankan ulang script ini."
}

docker compose up --build -d

Write-Host "Aplikasi berjalan:"
Write-Host "HTTPS: https://localhost:8443"
Write-Host "HTTP redirect: http://localhost:8080"
