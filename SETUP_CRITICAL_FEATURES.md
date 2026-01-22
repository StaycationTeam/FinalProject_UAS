# Setup Critical Features - Tumbal Perang Game

## 🚀 Quick Setup Guide

Setelah pull repository ini, jalankan langkah-langkah berikut:

### 1️⃣ Install Dependencies
```bash
composer install
npm install && npm run dev
```

### 2️⃣ Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Konfigurasi database di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wfp_final
DB_USERNAME=root
DB_PASSWORD=
```

### 3️⃣ Run Migrations
```bash
php artisan migrate:fresh --seed
```

Migrations yang akan dijalankan:
- ✅ Add `tribe_id` to users table
- ✅ Create `kingdom_buildings` pivot table
- ✅ Create `game_configs` table
- ✅ Add `is_active` to buildings table
- ✅ Set email unique constraint

### 4️⃣ Start Scheduler (PENTING!)

**Opsi A: Development (Manual)**
```bash
php artisan schedule:work
```
Jalankan command ini di terminal terpisah. Scheduler akan berjalan setiap menit.

**Opsi B: Production (Cron Job)**

Tambahkan ke crontab:
```bash
crontab -e
```

Tambahkan line ini:
```cron
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 5️⃣ Test Commands Manually

**Generate Gold:**
```bash
php artisan game:generate-gold
```
Output:
```
Starting gold generation...
Kingdom PlayerName: +5 gold (Total: 105)
Gold generation completed for 1/1 kingdoms.
```

**Produce Troops:**
```bash
php artisan game:produce-troops
```
Output:
```
Starting troop production...
Kingdom PlayerName: +5 troops from 1 barracks (Total: 15)
Troop production completed for 1/1 kingdoms.
```

---

## ✅ Critical Features Implemented

### 1. **Email Unique Validation** ✅
- Migration: `2026_01_22_103300_add_email_unique_to_users_table.php`
- Validasi di `AuthController@register`: `'email' => 'required|email|unique:users'`

### 2. **Tribe Selection saat Registration** ✅
- Migration: `2026_01_22_103000_add_tribe_id_to_users_table.php`
- User dapat memilih tribe (Marksman, Tank, Mage, Warrior) saat register
- Validasi: `'tribe_id' => 'required|exists:tribes,id'`

### 3. **Auto Gold Generation per Minute** ✅
- Command: `app/Console/Commands/GenerateGold.php`
- Default: 5 gold/menit untuk semua kingdom
- Tambahan: 10 gold/menit per mine
- Configurable via `game_configs` table

### 4. **Auto Troop Production per Minute** ✅
- Command: `app/Console/Commands/ProduceTroops.php`
- Default: 5 troops/menit per barracks
- Multiple barracks akan diakumulasikan
- Configurable via `game_configs` table

### 5. **Building Purchase System** ✅
- Route: `POST /kingdom/purchase-building`
- Controller: `KingdomController@purchaseBuilding()`
- Validasi gold sebelum purchase
- Deduct gold otomatis
- Support multiple buildings (increment quantity)

### 6. **Multiple Buildings Tracking** ✅
- Table: `kingdom_buildings` (pivot table)
- Columns: `kingdom_id`, `building_id`, `quantity`, `level`
- Track berapa banyak barracks, mines, walls per kingdom

### 7. **Attack Target List Filter** ✅
- Method: `Kingdom::canBeAttacked()`
- Filter: Kingdom harus punya barracks AND mine
- Controller: `BattleController@showBattle()`

### 8. **Battle Calculation Logic** ✅
- **Success (Attack > Defense)**:
  - Steal 90% defender gold
  - Defender troops berkurang
- **Failed (Attack ≤ Defense)**:
  - All attacker troops die
  - Defender troops survive: `(Defense - Attack) / troops_count`
- Configurable steal percentage via `game_configs`

---

## 📊 Game Configuration

Edit nilai konfigurasi via database table `game_configs`:

| Key | Default | Description |
|-----|---------|-------------|
| `default_gold_per_minute` | 5 | Gold dasar per menit |
| `gold_mine_production` | 10 | Gold per mine per menit |
| `barracks_troop_production` | 5 | Troops per barracks per menit |
| `attack_gold_steal_percentage` | 90 | % gold yang dicuri saat menang |

Ubah via SQL:
```sql
UPDATE game_configs SET value = '15' WHERE key = 'gold_mine_production';
```

Atau via PHP:
```php
GameConfig::setValue('default_gold_per_minute', 10);
```

---

## 🧪 Testing

### Test Email Unique:
```bash
# Register dengan email sama 2x
# Harus error: "The email has already been taken."
```

### Test Tribe Selection:
```bash
# Register tanpa pilih tribe
# Harus error: "The tribe id field is required."
```

### Test Gold Generation:
```bash
# Sebelum
SELECT gold FROM kingdoms WHERE id = 1;
# Hasil: 100

# Jalankan
php artisan game:generate-gold

# Sesudah
SELECT gold FROM kingdoms WHERE id = 1;
# Hasil: 105 (jika no mines) atau 115 (jika 1 mine)
```

### Test Troop Production:
```bash
# Build barracks dulu via UI
# Lalu jalankan
php artisan game:produce-troops

# Check troops
SELECT quantity FROM troops WHERE kingdom_id = 1;
# Harus bertambah 5 per barracks
```

### Test Building Purchase:
```bash
# Login ke game
# Go to Buildings page
# Click "Buy Barracks" (cost 50 gold)
# Gold harus berkurang 50
# Barracks count harus +1
```

### Test Attack Filter:
```bash
# Login ke game
# Go to Battle page
# Kingdom tanpa barracks/mine tidak muncul di list
```

---

## 🎮 Admin Panel

**Login:**
- URL: `/admin/login`
- Email: `admin@admin.com`
- Password: `admin123`

**Features:**
- Manage Buildings (CRUD)
- Manage Tribes attributes
- View statistics

---

## 📝 Database Seeding

Jika perlu reset data:
```bash
php artisan migrate:fresh --seed
```

Seeder akan create:
- 4 tribes (Marksman, Tank, Mage, Warrior)
- 4 buildings (Castle, Barracks, Mine, Walls)
- 1 admin user
- Game configs

---

## 🐛 Troubleshooting

### Scheduler tidak jalan:
```bash
# Pastikan ada di Kernel.php
php artisan schedule:list

# Jalankan manual
php artisan schedule:work
```

### Migration error:
```bash
# Clear cache dulu
php artisan config:clear
php artisan cache:clear

# Rollback & re-migrate
php artisan migrate:rollback
php artisan migrate
```

### Gold tidak bertambah:
```bash
# Check last_resource_update
SELECT id, name, gold, last_resource_update FROM kingdoms;

# Force generate
php artisan game:generate-gold
```

---

## 📚 Next Steps

Setelah critical features jalan:
1. ✅ Test semua fitur
2. ⏭️ Implement 5 bonus features
3. ⏭️ Deploy online (Heroku/Railway)
4. ⏭️ UI/UX improvements
5. ⏭️ Add email notifications

---

## 🔗 Links

- [Laravel Scheduler Docs](https://laravel.com/docs/10.x/scheduling)
- [Laravel Commands Docs](https://laravel.com/docs/10.x/artisan)
- [Game Requirements PDF](Final-Project-WFP-Gasal.pdf)
