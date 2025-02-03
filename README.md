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

## CRUD API MANAGEMENT
### 1. Tambah Tugas
**URL:** /add.php

**Metode:** POST

**Header yang Dibutuhkan:** 
```
Authorization: Bearer {token}
```

**Parameter:**
| Nama       | Tipe    | Deskripsi                              |
|------------|---------|----------------------------------------|
| judul      | string  | Judul Tugas                            |
| deskripsi  | string  | Deskripsi tugas                        |
| status     | string  | Status tugas (default: belum selesai)  |
| deadline   | date    | Deadline tugas (format YYYY-MM-DD)     |

**Respon Sukses:**
```
{
  "status": "success",
  "message": "Tugas berhasil ditambahkan",
  "id": 1
}
```

### 2. Daftar Tugas

**URL:** /list.php

**Metode:** GET

**Header yang Dibutuhkan:** 
```
Authorization: Bearer {token}
```

**Respon Sukses:**
```
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "judul": "Belajar PHP",
      "deskripsi": "Membuat API dengan PHP",
      "status": "belum selesai",
      "created_at": "2024-02-04",
      "deadline": "2024-02-10"
    }
  ],
  "total": 1
}
```

3.3 Perbarui Tugas
