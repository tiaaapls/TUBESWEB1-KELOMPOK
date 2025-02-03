# DOCUMENTATION - WEBSITE TODO LIST

## USER AUTHENTICATION API
### 1. Registrasi
**Endpoint:** **POST** /registrasi.php

**Parameter (Form Data):**

* nama_lengkap: Nama lengkap pengguna

* email: Alamat email pengguna

* user: Username pengguna

* pwd: Password pengguna

**Respon Sukses:**
```
{
  "status": "success",
  "message": "Registrasi berhasil"
}
```

**Respon Gagal:**
```
{
  "status": "error",
  "message": "Username atau email sudah digunakan"
}
```

### 2. Login
**Endpoint:** **POST** /login.php

**Parameter (Form Data):**

* user: Username pengguna

* pwd: Password pengguna

**Respon Sukses:**
```
{
  "status": "success",
  "session_token": "random_generated_token"
}
```

**Respon Gagal:**
```
{
  "status": "error",
  "message": "Username atau password salah"
}
```

### 3. Validasi Sesi

**Endpoint:** **POST** /session.php

**Parameter (Form Data):**

* session_token: Token sesi pengguna

**Respon Sukses:**
```
{
  "status": "success",
  "user_id": 1
}
```

**Respon Gagal:**
```
{
  "status": "error",
  "message": "Session tidak valid"
}
```

### 4. Logout Pengguna

**Endpoint:** **POST** /logout.php

**Parameter (Form Data):**

* session_token: Token sesi pengguna

**Respon Sukses:**
```
{
  "status": "success",
  "message": "Logout berhasil"
}
```

**Respon Gagal:**
```
{
  "status": "error",
  "message": "Token sesi tidak valid"
}
```

## CRUD API
