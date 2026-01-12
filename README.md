# 環境構築
<h2>Dockerビルド</h2>
<h4>・git clone git@github.com:sigyn08/attend.git</h4>
<h4>・docker-compose up -d --build</h4>
<h2>Laravel環境構築</h2>
<h4>docker-compose exec php bash</h4>
<h4>composer install</h4>
<h4>cp .env.example .env</h4>
<h4>php artisan key:generate</h4>
<h4>php artisan migrate</h4>
<h4>php artisan db:seed</h4>
<h2>使用技術（実行環境）</h2>
<h4>Laravel 8.83.29</h4>
<h4>PHP 8.2.29</h4>
<h4>MySQL 8.0.26</h4>
<h4>nginx 1.21.1</h4>
<h2>テーブル仕様</h2>
<img width="1138" height="367" alt="users" src="https://github.com/user-attachments/assets/32f5803c-5494-4afb-b41f-9553ac955d8e" />
<img width="1142" height="324" alt="attendances" src="https://github.com/user-attachments/assets/8db67a91-686f-4ca5-9301-868da7d6a076" />
<img width="1140" height="255" alt="break_times" src="https://github.com/user-attachments/assets/219855bc-dee9-452d-93c7-8e6c396309f0" />
<img width="1133" height="399" alt="stamp_correction" src="https://github.com/user-attachments/assets/18b48f48-31fe-4507-9a61-56169204de17" />
<h2>ER図</h2>
<img width="1319" height="725" alt="スクリーンショット 2026-01-10 112537" src="https://github.com/user-attachments/assets/72949b9f-1c8e-4421-9b5c-10b47e3ee759" />
<h2>テストアカウント</h2>
<h3>管理者ユーザー</h3>
<h4>name:管理者ユーザー</h4>
<h4>email:admin@example.com</h4>
<h4>password:adminpassword</h4>
<h3>一般ユーザー</h3>
<h4>name:西　怜奈</h4>
<h4>email:user@example.com</h4>
<h4>password:password</h4>
<h2>開発環境</h2>
<h4>ログイン画面：http://localhost/login</h4>
<h4>管理者ログイン画面：http://localhost/admin/login</h4>
<h4>phpMyAdmin：http://localhost:8080/index.php</h4>

