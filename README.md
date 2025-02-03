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
