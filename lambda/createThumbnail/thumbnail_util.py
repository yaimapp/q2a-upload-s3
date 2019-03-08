# -*- config: utf-8 -*-
from PIL import Image, JpegImagePlugin
import os, re, boto3

s3 = boto3.client('s3')
MOBILE_THUMB_SIZE = int(os.environ['MOBILE_THUMB_SIZE'])
IMAGE_QUALITY = int(os.environ['IMAGE_QUALITY'])
CONTENT_TYPES = {
  'JPEG': 'image/jpeg',
  'PNG': 'image/png',
  'GIF': 'image/gif'
}

def convert_image(event, context):
    srcBucket = event['Records'][0]['s3']['bucket']['name']
    destBucket = srcBucket
    destPrefix = 'thumb/'
    key = event['Records'][0]['s3']['object']['key']
    orgImagePath = '/tmp/' + os.path.basename(key)
    
    print("convert started: " + key)
    if re.match(destPrefix, key):
        return {
            'message': 'This File Is the Thumbnail.'
        }

    try:
        s3.download_file(Bucket=srcBucket, Key=key, Filename=orgImagePath)
        JpegImagePlugin._getmp = lambda x: None

        orgImage = Image.open(orgImagePath, 'r')
        orgImageFormat = orgImage.format
        if (orgImageFormat != 'JPEG' and orgImageFormat != 'PNG' and orgImageFormat != 'GIF'):
            return {
                'message': "This file is not supported."
            }
        # 元画像の画質を変えて保存
        save_image(orgImage, orgImagePath, orgImageFormat)
        s3.upload_file(Filename=orgImagePath, Bucket=destBucket, Key=key, ExtraArgs={"ContentType": CONTENT_TYPES[orgImageFormat]})
        print("originalImage Change quality: " + key)
    
        thumbImage = Image.open(orgImagePath, 'r')
        thumbFormart = thumbImage.format
        thumbWidth = thumbImage.width
        # MOBILE_THUMB_SIZE より大きな画像をリサイズ
        if (thumbWidth > MOBILE_THUMB_SIZE):
            thumbImage.thumbnail((MOBILE_THUMB_SIZE, MOBILE_THUMB_SIZE), Image.LANCZOS)
        # サムネイル画像を保存
        save_image(thumbImage, orgImagePath, thumbFormart)
        thumbDestPath = destPrefix + key
        s3.upload_file(Filename=orgImagePath, Bucket=destBucket, Key=thumbDestPath, ExtraArgs={"ContentType": CONTENT_TYPES[thumbFormart]})
        print("Thumbnail Created: " + thumbDestPath)
        return {
            'message': "Image Conversion Succeeded."
        }
    except Exception as e:
        print(e)
        raise e

def save_image(image, path, format):
    if format == 'JPEG':
        image.save(path, 'JPEG', quality=IMAGE_QUALITY, optimize=True, progressive=True)
    else:
        image.save(path, optimize=True)