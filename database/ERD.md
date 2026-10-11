# ERD NusaWear

```text
users (1) -------- (1) user_profiles
  |
  | (1)
  | 
  +----------------< stock_movements >---------------- products
                                                       >---------------- categories (1)
products >----------------< product_suppliers >---------------- suppliers
```

## Relasi
- `users` ke `user_profiles`: one-to-one; `user_profiles.user_id` memiliki UNIQUE.
- `categories` ke `products`: one-to-many; foreign key `products.category_id`.
- `products` ke `stock_movements`: one-to-many; histori pergerakan stok.
- `users` ke `stock_movements`: one-to-many; setiap transaksi dicatat oleh pengguna.
- `products` ke `suppliers`: many-to-many melalui `product_suppliers`.

## Business rules
1. SKU produk harus unik.
2. Harga dan quantity tidak boleh negatif; quantity transaksi harus lebih dari nol.
3. Barang keluar tidak boleh melebihi stok yang tersedia.
4. Stok tidak boleh negatif.
5. Produk dengan histori tidak dihapus sembarangan karena foreign key menggunakan RESTRICT.
6. Halaman internal memerlukan login.
7. Staff tidak dapat membuka halaman administrasi pengguna.
8. Sorting memakai daftar kolom yang diizinkan.
9. Update stok dan penyimpanan histori dilakukan dalam transaction, lalu commit atau rollback.
