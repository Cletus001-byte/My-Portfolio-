Add-Type -AssemblyName System.Drawing

$srcDir = 'C:\xampp\htdocs\cletusportfolio\wp-content\plugins\cletus-portfolio-core\assets\images'
$optDir = 'C:\xampp\htdocs\cletusportfolio\wp-content\plugins\cletus-portfolio-core\assets\images\optimized'

if (-not (Test-Path -Path $optDir)) {
    New-Item -ItemType Directory -Path $optDir | Out-Null
}

$jpegCodec = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object { $_.MimeType -eq 'image/jpeg' }
$encoderParams = New-Object System.Drawing.Imaging.EncoderParameters(1)
$encoderParams.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, 85L)

for ($i = 1; $i -le 12; $i++) {
    $srcFile = Join-Path $srcDir "$i.jpg"
    if (Test-Path -Path $srcFile) {
        $img = [System.Drawing.Image]::FromFile($srcFile)
        $origW = $img.Width
        $origH = $img.Height
        
        $maxDim = 800
        if ($origW -gt $maxDim -or $origH -gt $maxDim) {
            if ($origW -ge $origH) {
                $newW = $maxDim
                $newH = [int][Math]::Round(($origH / $origW) * $maxDim)
            } else {
                $newH = $maxDim
                $newW = [int][Math]::Round(($origW / $origH) * $maxDim)
            }
        } else {
            $newW = $origW
            $newH = $origH
        }
        
        $bmp = New-Object System.Drawing.Bitmap($newW, $newH)
        $g = [System.Drawing.Graphics]::FromImage($bmp)
        $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
        $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
        $g.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
        $g.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
        
        $g.DrawImage($img, 0, 0, $newW, $newH)
        
        $destFile = Join-Path $optDir "$i.jpg"
        $bmp.Save($destFile, $jpegCodec, $encoderParams)
        
        $g.Dispose()
        $bmp.Dispose()
        $img.Dispose()
        
        $origSize = (Get-Item $srcFile).Length
        $optSize = (Get-Item $destFile).Length
        $savings = [Math]::Round((1 - ($optSize / $origSize)) * 100, 1)
        
        Write-Output "Optimized $i.jpg: ${newW}x${newH} | $([Math]::Round($optSize / 1KB, 2)) KB (Saved $savings%)"
    }
}