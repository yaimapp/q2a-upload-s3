## コマンドでs3ファイルを変換する手順
- aws cli には python が必要です
- また、parallel というコマンドの利用には ruby 2.2 以上が必要です

### aws cli インストール（Macの場合）
```
$ pip install awscli --upgrade --user
```
詳しくはこちら
- [macOS で AWS Command Line Interface をインストールする \- AWS Command Line Interface](https://docs.aws.amazon.com/ja_jp/cli/latest/userguide/install-macos.html)


### AWS CLI 実行ユーザーの作成
`IAM` でユーザーを作成してグループ `CallLambdaInvoke` に追加する

### aws の設定
あらかじめコマンドラインを実行するIAMユーザーを作成してACCESS KEY IDとSECRET ACCESS KEYを取得しておく
```
$ aws configure
AWS Access Key ID [None]: {ACCESS_KEY_ID}
AWS Secret Access Key [None]: {SECRET_ACCESS_KEY}
Default region name [None]: us-west-2 <= バケットのregionに合わせる
Default output format [None]: json
```

### 必要なコマンド、ライブラリのインストール
```
$ sudo yum install -y parallel <= Amazon linux
or
$ brew install parallel <= MacOS
# 共通
$ gem install aws-sdk parallel
```

### S3 Bucket からファイル一覧を取得（Backet名は適宜書き換える)
```
$ aws s3 ls --region us-west-2 --recursive s3://test.38qa.net/ | ruby -n -e 'print $_.split(/\s+/, 4)[3] if (/^(?!.*(thumb|test)).+$/i && /\.(jpe?g|png|gif)/i)' > todo.txt
```

### ruby スクリプトの実行
実行する前にスクリプト内のBucket名、関数名を確認する
```
$ ruby run_convert_image.rb
```