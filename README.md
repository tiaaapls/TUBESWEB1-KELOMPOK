# DOCUMENTATION - WEBSITE TODO LIST

## USER AUTHENTICATION API
### 1. Registrasi
* **URL:** /registrasi.php

* **Metode:** POST

* **Parameter (Form Data):**

    + nama_lengkap: Nama lengkap pengguna
    
    + email: Alamat email pengguna
    
    + user: Username pengguna
    
    + pwd: Password pengguna

* **Respon Sukses:**
```
{
  "status": "success",
  "message": "Registrasi berhasil"
}
```

* **Respon Gagal:**
```
{
  "status": "error",
  "message": "Username atau email sudah digunakan"
}
```

### 2. Login
* **URL:** /login.php

* **Metode:** POST

* **Parameter (Form Data):**

    + user: Username pengguna
    
    + pwd: Password pengguna

* **Respon Sukses:**
```
{
  "status": "success",
  "session_token": "random_generated_token"
}
```

* **Respon Gagal:**
```
{
  "status": "error",
  "message": "Username atau password salah"
}
```

### 3. Validasi Sesi
* **URL:** /session.php

* **Metode:** POST

* **Parameter (Form Data):**

    + session_token: Token sesi pengguna

* **Respon Sukses:**
```
{
  "status": "success",
  "user_id": 1
}
```

* **Respon Gagal:**
```
{
  "status": "error",
  "message": "Session tidak valid"
}
```

### 4. Logout Pengguna
* **URL:** /logout.php

* **Metode:** POST

* **Parameter (Form Data):**

    + session_token: Token sesi pengguna

* **Respon Sukses:**
```
{
  "status": "success",
  "message": "Logout berhasil"
}
```

* **Respon Gagal:**
```
{
  "status": "error",
  "message": "Token sesi tidak valid"
}
```

## API MANAGEMENT TUGAS

Semua endpoint API memerlukan token sesi yang dikirim melalui header **Authorization: Bearer {token}**. Token ini harus divalidasi sebelum pengguna dapat mengakses data tugas mereka.

### 1. Tambah Tugas
* **URL:** /add.php

* **Metode:** POST

* **Header:** 
```
Authorization: Bearer {token}
```

* **Parameter:**

| Nama       | Tipe    | Wajib  | Deskripsi                              |
|------------|---------|--------|----------------------------------------|
| judul      | string  | Ya     | Judul tugas                            |
| deskripsi  | string  | Ya     | Deskripsi tugas                        |
| status     | string  | Ya     | Status tugas (default: belum selesai)  |
| deadline   | date    | Ya     | Deadline tugas (format YYYY-MM-DD)     |

* **Respon Sukses:**
```
{
  "status": "success",
  "message": "Tugas berhasil ditambahkan",
  "id": 1
}
```

### 2. Daftar Tugas
* **URL:** /list.php

* **Metode:** GET

* **Header:** 
```
Authorization: Bearer {token}
```

* **Respon Sukses:**
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

### 3. Perbarui Tugas
* **URL:** /update.php

* **Metode:** POST

* **Header:** 
```
Authorization: Bearer {token}
```

* **Parameter:**

| Nama       | Tipe    | Wajib  | Deskripsi                                       |
|------------|---------|--------|-------------------------------------------------|
| id         | int     | Ya     | ID tugas yang akan diperbarui                   |
| judul      | string  | Tidak  | Judul tugas baru                                |
| deskripsi  | string  | Tidak  | Deskripsi tugas baru                            |
| status     | string  | Tidak  | Status tugas baru (belum selesai atau selesai)  |
| deadline   | date    | Tidak  | Deadline tugas baru                             |

* **Respon Sukses:**
```
{
  "status": "success",
  "message": "Tugas berhasil diperbarui"
}
```

### 4. Hapus Tugas
* **URL:** /delete.php

* **Metode:** POST

* **Header:** 
```
Authorization: Bearer {token}
```

* **Parameter:**

| Nama       | Tipe    | Wajib  | Deskripsi                     |
|------------|---------|--------|-------------------------------|
| id         | int     | Ya     | ID tugas yang akan dihapus    |

### 5. Cari Tugas
* **URL:** /search.php

* **Metode:** GET

* **Header:** 
```
Authorization: Bearer {token}
```

* **Parameter:**

| Nama                    | Tipe    | Wajib  | Deskripsi                     |
|-------------------------|---------|--------|-------------------------------|
| keyword (status/judul)  | string  | Ya     | ID tugas yang akan dihapus    |

* **Respon Sukses:**
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
  ]
}
```

### 6. Notifkasi Tugas
* **URL:** /notifikasi.php

* **Metode:** GET

*  **Deskripsi:** Mengambil daftar tugas yang akan jatuh tempo dalam 3 hari dan statistik tugas berdasarkan statusnya.

* **Header:** 
```
Authorization: Bearer {token}
```

* **Respon Sukses:**
```
{
  "status": "success",
  "data": {
    "tugas_deadline_segera": [
      {
        "id": 1,
        "judul": "Tugas Matematika",
        "deskripsi": "Kerjakan soal dari halaman 45",
        "deadline": "2024-11-10"
      }
    ],
    "statistik": {
      "belum_selesai": 3,
      "sedang_dikerjakan": 2,
      "selesai_hari_ini": 1
    }
  }
}
```

### 7. Perbarui Password
* **URL:** /update_password.php

* **Metode:** POST

*  **Deskripsi:** Memperbarui password pengguna dengan validasi password lama.

* **Header:** 
```
Authorization: Bearer {token}
```

* **Parameter:**

| Nama              | Tipe    | Wajib  |  
|-------------------|---------|--------|
| current_password  | string  | Ya     |
| new_password      | string  | Ya     |
| confirm_password  | string  | Ya     |

* **Respon Sukses:**
```
{
  "status": "success",
  "message": "Password berhasil diperbarui"
}
```

* **Respon Error:**
```
{
  "status": "error",
  "message": "Unauthorized"
}
```

### 8. Perbarui Profil
* **URL:** /update_profile.php

* **Metode:** POST

*  **Deskripsi:** Memperbarui informasi profil pengguna termasuk foto profil.

* **Header:** 
```
Authorization: Bearer {token}
```

* **Parameter:**

| Nama          | Tipe    | Wajib  |  
|---------------|---------|--------|
| nama_lengkap  | string  | Tidak  |
| email         | string  | Tidak  |
| username      | string  | Tidak  |
| foro_profil   | file    | Tidak  |

* **Respon Sukses:**
```
{
  "status": "success",
  "message": "Profil berhasil diperbarui",
  "foto_profil": "uploads/profiles/{filename}"
}
```

* **Respon Error:**
```
{
  "status": "error",
  "message": "Kesalahan database: {error_message}"
}
```
## Statistic API
### 1. Jumlah Tugas Belum Selesai
* **URL:** /sum_tugas_belumSelesai.php

* **Metode:** POST

*  **Deskripsi:** Menghitung jumlah tugas yang belum selesai.

*  **Respons Sukses:**
```
[
    {
        "jumlah_tugas_belumSelesai": 5
    }
]
```

### 2. Jumlah Tugas Selesai
* **URL:** /sum_tugas_selesai.php

* **Metode:** POST

*  **Deskripsi:** Menghitung jumlah tugas yang selesai.

*  **Respons Sukses:**
```
[
    {
        "jumlah_tugas_selesai": 5
    }
]
```

### 3. Jumlah Tugas Per Minggu dalam Bulan Tertentu
* **URL:** /sum_tugasPerbulan.php

* **Metode:** POST

*  **Deskripsi:** Menghitung jumlah tugas perminggu dalam bulan tertentu.

*  **Parameter:**
  
| Nama    | Tipe    | Deskripsi                                         |  
|---------|---------|---------------------------------------------------|
| bulan   | string  | Nama bulan (contoh: "Januari", "Februari", dll.)  |

*  **Respons Sukses:**
```
[
    {
        "minggu": 1,
        "jumlah_tugas": 3
    },
    {
        "minggu": 2,
        "jumlah_tugas": 5
    }
]
```
