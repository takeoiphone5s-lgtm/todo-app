# 困ったときは

## まず落ち着いて、エラー画面を読む

`APP_DEBUG=true` のとき、Laravel は **かなり親切なエラー画面** を出します。

見るべき場所は 3 つです。

1. **一番上の太字** … エラーの種類と内容（英語でも、翻訳にかければすぐ分かります）
2. **ファイル名と行番号** … どこで起きたか
3. **ハイライトされたコード** … その行に何が書いてあるか

「エラーが出た」と言う前に、**この 3 つをメモしてから** 質問すると解決が早くなります。

> エラー画面が出ずに真っ白なときは、`storage/logs/laravel.log` の一番下を見てください。

---

## 環境まわり

### `php` は内部コマンドまたは外部コマンドとして認識されていません

PATH に PHP が入っていません。

- **XAMPP**：Control Panel の右にある **Shell** ボタンから黒い画面を開けば、そこでは `php` が使えます
- 恒久的に直すなら、Windows の環境変数 PATH に `C:\xampp\php` を追加してください

### `SQLSTATE[HY000] [2002] 接続できませんでした` / `Connection refused`

MySQL が起動していません。XAMPP Control Panel で **MySQL の Start** を押してください。

### `SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'`

`.env` のユーザー名・パスワードが違います。XAMPP では次が正しい状態です。

```env
DB_USERNAME=root
DB_PASSWORD=
```

直したら `php artisan config:clear` を実行してください。

### `SQLSTATE[HY000] [1049] Unknown database 'todo_app'`

データベースを作っていません。

**この PC は電源を切るとデータが消えるので、授業のたびに作り直しが必要です。**
「前回は動いたのに」というときは、たいていこれです。

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS todo_app DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

詳しくは [第 0 章 Step 0-5](00-setup.md) を見てください。

### テーブルはあるのに、前回入れたタスクが消えている

**正常です。** この PC はシャットダウンでデータベースの中身も消えます。

ダミーデータを入れ直すには（第 3 章まで進んでいる人）：

```powershell
php artisan migrate:fresh --seed
```

### `could not find driver`

PHP の MySQL 拡張が無効です。`php.ini` の次の行の先頭の `;` を消して、MySQL を再起動します。

```ini
extension=pdo_mysql
extension=fileinfo
```

`php --ini` で、どの `php.ini` が読まれているか確認できます。

### `No application encryption key has been specified.`

```bash
php artisan key:generate
```

### `Failed to listen on 127.0.0.1:8000`

すでに `php artisan serve` が動いています。別のポートを使うか、前のターミナルを閉じてください。

```bash
php artisan serve --port=8001
```

---

## ルーティング・URL まわり

### `404 | NOT FOUND`

| 確認すること |
| --- |
| URL のスペルは合っているか（`/task` ではなく `/tasks`） |
| `routes/web.php` を保存したか |
| `php artisan route:list` にそのルートが出ているか |
| `findOrFail()` で「データが無い」場合も 404 になります |

### `Route [tasks.store] not defined.`

そのルート名が登録されていません。

```bash
php artisan route:list --path=tasks
```

で確認してください。`Route::resource(...)->only([...])` の配列に、
その章で追加するはずのメソッド名が入っているか見直します。

### `The GET method is not supported for this route. Supported methods: POST.`

`<form>` に `method="post"` を書き忘れています。

### `The POST method is not supported for this route. Supported methods: PUT, PATCH.`

`@method('PUT')` または `@method('PATCH')` を書き忘れています。
`<form method="post">` はそのままで大丈夫です。

---

## フォームまわり

### `419 | PAGE EXPIRED`

`@csrf` を書き忘れています。**`method="post"` の次の行は必ず `@csrf`。**

（何度書いても出る場合は、ブラウザのタブを長時間開きっぱなしでトークンが失効しています。再読み込みしてください）

### 入力した値が保存されない／空で保存される

| 確認すること |
| --- |
| `<input name="name">` と `$request->input('name')` の名前が一致しているか |
| モデルの `$fillable` に そのカラム名 が入っているか |
| `<input>` が `<form>` タグの **内側** にあるか |

### エラーメッセージが表示されない

`@error('name')` の `'name'` が、`<input name="...">` と一致しているか確認してください。

---

## ビューまわり

### `View [tasks.index] not found.`

`resources/views/tasks/index.blade.php` があるか確認してください。

- 拡張子は `.blade.php`（`.php` だけではダメ）
- `view('tasks.index')` のドットは **フォルダの区切り**

### `Undefined variable $tasks`

コントローラからビューに変数を渡していません。

```php
return view('tasks.index', compact('tasks'));
```

`compact()` に渡す名前は **変数名から `$` を取ったもの** です。

### 変更したのに画面に反映されない

```bash
php artisan view:clear
```

そのうえで、ブラウザで **`Ctrl + Shift + R`**（Mac は `Cmd + Shift + R`）。

### CSS が効かない

- `public/css/app.css` があるか
- レイアウトに `<link rel="stylesheet" href="{{ asset('css/app.css') }}">` があるか
- ブラウザの開発者ツール（F12）→ Network タブで `app.css` が 200 で読めているか

---

## モデル・DB まわり

### `Class "Task" not found` と出る（名前空間が付いていない）

**`app/Models/Task.php` はちゃんとあるのに、Tinker でこう言われるケースです。**
主に `php artisan tinker` の中で起きます。

```
>>> Task::create(['name' => '牛乳を買う', 'status' => false]);
  Class "Task" not found.
```

#### 原因

PHP は「クラス名 → ファイルの場所」の対応表
（`vendor/composer/autoload_classmap.php`）を使ってファイルを探します。
この表は **`composer install` を実行した時点のファイル一覧** から作られます。

第 0 章で `composer install` した時点では `Task.php` はまだ存在しないので、
**第 3 章で作った `Task` は、この表に載っていません。**

- アプリ本体（コントローラ・シーダー・テスト）は `App\Models\Task` と
  **フルネーム** で書くので、表になくてもフォルダ構成から場所を推測して読み込めます
- ところが **Tinker の「短い名前で呼べる機能」は、この表だけを見ています**

だから「テストやブラウザでは動くのに、Tinker の `Task::` だけ落ちる」という状態になります。

#### 対処

```powershell
composer dump-autoload
```

これで表が作り直されます。次に `php artisan tinker` を起動すると

```
[!] Aliasing 'Task' to 'App\Models\Task' for this Tinker session.
```

と表示され、`Task::count()` のような短い書き方が使えるようになります。

> **この PC は電源を切ると中身が消えるので、授業のたびに 1 回必要です。**
> 第 3 章の Step 3-3 に手順があります。

#### 急いでいるときの回避策

対応表を直さずに、フルネームで打っても動きます。

```php
App\Models\Task::create(['name' => '牛乳を買う', 'status' => false]);
```

> なお 2 回目以降の授業で、**`Task.php` がある状態でクローンして `composer install` した人は、
> 最初からこの表に `Task` が載っているので、このエラーが出ません。**
> 同じ教室で「出る人」と「出ない人」が分かれるのはこれが理由です。

### `Class "App\Models\Task" not found` と出る

**このエラーは「Task モデルのファイルを PHP が見つけられない」という意味** です。
よくある原因を、起きやすい順に並べました。上から順に確認してください。

#### 原因 1：まだ第 3 章に進んでいない（一番多い）

`Task` モデルを作るのは **第 3 章** です。
第 2 章の時点で `php artisan test`（フィルタなし）を実行すると、
第 3 章以降のテストも一緒に走るので、このエラーがずらっと出ます。

**この段階では正常です。** 第 2 章の確認は、章を指定して実行してください。

```powershell
php artisan test --filter=ch02
```

#### 原因 2：ファイルの置き場所が違う

正しい場所は **`app/Models/Task.php`** です。次のコマンドで確認できます。

```powershell
Get-ChildItem app\Models
```

`Task.php` が出てこない場合、`app/Task.php` に作ってしまっているか、
そもそも作れていません。`php artisan make:model Task` を実行し直してください
（Laravel 7 以前の解説記事は `app/` 直下に作らせるので、見比べるときは注意）。

#### 原因 3：拡張子が `.php` になっていない（Windows で頻発）

Windows は初期設定で **拡張子を隠します**。
メモ帳でモデルを作ると、`Task.php` のつもりが **`Task.php.txt`** になっていることがあります。

```powershell
Get-ChildItem app\Models -Force | Select-Object Name, Length
```

`Task.php.txt` のような名前が出たら、これが原因です。
エクスプローラーの「表示」→「ファイル名拡張子」にチェックを入れてから直してください。

> そもそも **`php artisan make:model Task` で作れば、この事故は起きません。**
> 手でファイルを作らないのが一番の対策です。

#### 原因 4：`namespace` が間違っている

ファイルの先頭は、必ずこの 2 行です。

```php
<?php

namespace App\Models;
```

`App\Model`（単数形）や `app\Models`（小文字）になっていると見つかりません。
`namespace` の行を書き忘れた場合も同じです。

#### 原因 5：`use` 文を書き忘れている

コントローラやシーダーの先頭に、次の行が必要です。

```php
use App\Models\Task;
```

> **なお `php artisan tinker` の中では `use` 文は不要**（`Task::count()` がそのまま動く）です。
> tinker では動くのにテストで落ちる場合は、この `use` 文の書き忘れを疑ってください。

#### 原因 6：クラス名とファイル名が合っていない

`app/Models/Task.php` の中身は `class Task extends Model` でなければいけません。
`class Tasks`（複数形）や `class task`（小文字）になっていないか確認してください。

#### それでも直らないとき

クラスの索引（オートロード情報）を作り直します。

```powershell
composer dump-autoload
```

### `Table 'todo_app.tasks' doesn't exist`

`php artisan migrate` を実行していません。

### マイグレーションファイルを直したのに反映されない

Laravel は「実行済み」を記録しているので、同じファイルを再実行しません。

```bash
php artisan migrate:rollback   # 取り消す
# ← ここでファイルを修正
php artisan migrate            # もう一度実行
```

どうにもならなくなったら、**データが消えてもよければ**

```bash
php artisan migrate:fresh --seed
```

### `Attempt to assign property "..." on null`

`find()` がデータを見つけられず `null` を返しています。
`findOrFail()` に変えると、原因が分かりやすい 404 になります。

### `Add [name] to fillable property to allow mass assignment.`

`app/Models/Task.php` の `$fillable` にそのカラム名を追加してください。

---

## 型まわり（第 11 章）

### `strict_types declaration must be the very first statement in the script`

`declare(strict_types=1);` は `<?php` の **直後** に書きます。
コメントや `namespace` より後ろに置くと、この致命的エラーになります。

```php
<?php

declare(strict_types=1);   // ← ここ

namespace App\Http\Controllers;
```

### `Return value must be of type X, Y returned`

メソッドに書いた戻り値の型と、実際に `return` しているものが違います。

| 返しているもの | 正しい型 |
| --- | --- |
| `view(...)` | `View` |
| `redirect()->...` | `RedirectResponse` |

### `Argument #1 ($task) must be of type App\Models\Task, string given`

ルートモデルバインディングが効いていません。
**ルート定義の `{...}` の名前と、引数名を一致させてください。**

```php
Route::patch('/tasks/{task}/complete', ...);   // {task}
public function complete(Task $task)           // $task
```

### `403 | THIS ACTION IS UNAUTHORIZED.`

FormRequest の `authorize()` が `false` のままです。`true` に変えてください。
`php artisan make:request` の初期値が `false` なので、非常によくある詰まりどころです。

### `Type of App\Models\Task::$fillable must not be defined`

`protected array $fillable` のように型を付けてしまっています。
親の `Model` が型なしで宣言しているため、子クラスで型を足すことは PHP の仕様上できません。
`protected $fillable` に戻し、型は PHPDoc（`@var array<int, string>`）で伝えてください。

### `Class "App\Http\Requests\TaskRequest" not found`

`php artisan make:request TaskRequest` を実行したか、
コントローラに `use App\Http\Requests\TaskRequest;` を書いたか確認してください。

---

## テストまわり

### 全部失敗する

まだ機能を作っていないだけかもしれません。まずは対象の章まで進めてください。

### `Database file at path [:memory:] does not exist`

`pdo_sqlite` 拡張が無効です。`php.ini` の `extension=pdo_sqlite` の `;` を消してください。

### テストを実行したら開発データが消えた

このリポジトリの `phpunit.xml` では、テストは **メモリ上の SQLite** を使う設定なので、
本来 MySQL のデータは消えません。消えた場合は `phpunit.xml` の

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

がコメントアウトされていないか確認してください。

---

## 最終手段

```bash
php artisan optimize:clear
composer dump-autoload
```

それでもダメなら、**エラーメッセージ全文** と **直前に何をしたか** を持って講師に相談してください。
「動きません」だけでは誰にも分かりません。

---

## Git で「元に戻す」

作業中にぐちゃぐちゃになったら、変更を捨てて最後のコミットに戻せます。

```bash
git status              # 何を変更したか確認
git diff                # 変更内容を確認
git checkout -- ファイル名   # そのファイルの変更を捨てる
```

**捨てた変更は戻せません。** 実行前に必ず `git diff` で確認してください。

こまめにコミットしておくと、安心して実験できます。

```bash
git add .
git commit -m "第5章まで完了"
```
