#!/bin/bash

# Create necessary directories
mkdir -p /Users/gideonaina/Documents/Gideons-Technology/assets/img/blog
mkdir -p /Users/gideonaina/Documents/Gideons-Technology/assets/img/seller
mkdir -p /Users/gideonaina/Documents/Gideons-Technology/assets/img/footer

# Function to create a placeholder image using base64 data
create_placeholder_image() {
    local file_path=$1
    local width=$2
    local height=$3
    local bg_color=$4
    local ext="${file_path##*.}"
    
    # Create a transparent placeholder PNG
    if [ "$ext" == "png" ]; then
        echo "Creating PNG placeholder: $file_path ($width x $height)"
        convert -size ${width}x${height} xc:transparent -fill "$bg_color" -draw "rectangle 1,1 ${width},${height}" "$file_path"
    # Create a JPEG placeholder
    elif [ "$ext" == "jpg" ] || [ "$ext" == "jpeg" ]; then
        echo "Creating JPEG placeholder: $file_path ($width x $height)"
        convert -size ${width}x${height} xc:"$bg_color" "$file_path"
    # Create a JFIF placeholder (same as JPEG)
    elif [ "$ext" == "jfif" ]; then
        echo "Creating JFIF placeholder: $file_path ($width x $height)"
        convert -size ${width}x${height} xc:"$bg_color" "$file_path"
    else
        echo "Unsupported format for $file_path"
    fi
}

# Check if ImageMagick is installed
if ! command -v convert &> /dev/null; then
    echo "ImageMagick is not installed. Creating empty files instead."
    # Create empty placeholder files
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/logo-01.png
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/icon.png
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/ecommerce.jfif
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/landing.png
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/schoolmanagement.jfif
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/agricultural.jfif
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/webdev.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/videoediting.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/repairs.png
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/kar.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/fintech.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/services.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/client1.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/client2.jpg

    # Blog folder images
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/futurebusiness.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/tech.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/blockchain.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/iot.jpg
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/greent.jpg

    # Seller folder images
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/seller/seller.jpg

    # Footer folder images
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/footer/c1.png
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/footer/c2.png
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/footer/c3.png
    touch /Users/gideonaina/Documents/Gideons-Technology/assets/img/footer/c4.png
else
    # Create actual placeholder images with ImageMagick
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/logo-01.png" 200 80 "#4285f4"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/icon.png" 32 32 "#4285f4"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/ecommerce.jfif" 400 300 "#34a853"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/landing.png" 800 600 "#fbbc05"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/schoolmanagement.jfif" 400 300 "#ea4335"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/agricultural.jfif" 400 300 "#4285f4"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/webdev.jpg" 400 300 "#34a853"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/videoediting.jpg" 400 300 "#fbbc05"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/repairs.png" 400 300 "#ea4335"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/kar.jpg" 400 300 "#4285f4"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/fintech.jpg" 400 300 "#34a853"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/services.jpg" 400 300 "#fbbc05"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/client1.jpg" 150 150 "#ea4335"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/client2.jpg" 150 150 "#4285f4"

    # Blog folder images
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/futurebusiness.jpg" 400 300 "#34a853"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/tech.jpg" 400 300 "#fbbc05"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/blockchain.jpg" 400 300 "#ea4335"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/iot.jpg" 400 300 "#4285f4"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/blog/greent.jpg" 400 300 "#34a853"

    # Seller folder images
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/seller/seller.jpg" 400 300 "#fbbc05"

    # Footer folder images
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/footer/c1.png" 150 50 "#ea4335" 
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/footer/c2.png" 150 50 "#4285f4"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/footer/c3.png" 150 50 "#34a853"
    create_placeholder_image "/Users/gideonaina/Documents/Gideons-Technology/assets/img/footer/c4.png" 150 50 "#fbbc05"
fi

echo "Image placeholders created successfully!"