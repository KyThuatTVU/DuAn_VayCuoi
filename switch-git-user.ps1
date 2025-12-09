# Script chuyển đổi Git user để commit
# Sử dụng: .\switch-git-user.ps1

Write-Host "=== CHỌN TÀI KHOẢN GIT ĐỂ COMMIT ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Thuật (KyThuatTVU)" -ForegroundColor Green
Write-Host "2. ThaoVy (1ThaoVy)" -ForegroundColor Yellow  
Write-Host "3. Trường (NguyenNhatTruong26101997)" -ForegroundColor Magenta
Write-Host ""

$choice = Read-Host "Nhập số (1-3)"

switch ($choice) {
    "1" {
        git config user.name "KyThuatTVU"
        git config user.email "nguyenhuynhkithuat84tv@gmail.com"
        Write-Host "`nĐã chuyển sang: KyThuatTVU" -ForegroundColor Green
    }
    "2" {
        git config user.name "1ThaoVy"
        git config user.email "thaoVy@gmail.com"  # Thay bằng email thật của ThaoVy
        Write-Host "`nĐã chuyển sang: 1ThaoVy" -ForegroundColor Yellow
    }
    "3" {
        git config user.name "NguyenNhatTruong26101997"
        git config user.email "truong@gmail.com"  # Thay bằng email thật của Trường
        Write-Host "`nĐã chuyển sang: NguyenNhatTruong26101997" -ForegroundColor Magenta
    }
    default {
        Write-Host "`nLựa chọn không hợp lệ!" -ForegroundColor Red
        exit
    }
}

Write-Host ""
Write-Host "Thông tin Git hiện tại:" -ForegroundColor Cyan
git config user.name
git config user.email
