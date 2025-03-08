# Symfony Book Management Project

# 📌 Opis

- Symfony 7.1.3
- PHP 8.3.10
- PostgreSQL
- Docker
- Full Rest API
- Deploy na moim serwerze
- Frontend: [Github Pages](https://sirdomin.github.io/company-app-frontend/)

# 📌 Funkcjonalności

## **1. Zarządzanie Firmami ([CompanyController](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php))**

### 🔹 **Operacje na firmach**
- **Dodawanie Firmy** ([create_company](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php#L24))
   - Obsługuje walidację danych wejściowych.
   - Weryfikuje unikalność numeru VAT za pomocą `UniqueVatValidator`.
   - Tworzy nową firmę przy użyciu `CompanyFactory`.
   - Zapisuje do bazy danych przez `CompanyRepository`.
- **Edycja Firmy** ([update_company](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php#L85))
   - Pobiera firmę z `CompanyRepository`.
   - Aktualizuje dane firmy i zapisuje zmiany.
- **Usuwanie Firmy** ([delete_company](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php#L100))
   - Usuwa firmę z bazy danych.
- **Wyświetlanie listy Firm** ([get_companies](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php#L59))
   - Obsługuje paginację i wyszukiwanie po nazwie firmy oraz NIP.

## **2. Zarządzanie Pracownikami ([EmployeeController](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php))**

### 🔹 **Operacje na pracownikach**
- **Dodawanie Pracownika** ([create_employee](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php#L24))
   - Sprawdza poprawność danych wejściowych.
   - Upewnia się, że przypisana firma istnieje w bazie danych.
   - Tworzy nowego pracownika przy użyciu `EmployeeFactory`.
   - Zapisuje przez `EmployeeRepository`.
- **Edycja Pracownika** ([update_employee](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php#L85))
   - Pobiera pracownika z `EmployeeRepository`, weryfikuje czy istnieje.
   - Aktualizuje dane pracownika.
- **Usuwanie Pracownika** ([delete_employee](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php#L107))
   - Usuwa pracownika z bazy danych.
- **Wyświetlanie listy Pracowników** ([get_employee](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php#L59))
   - Obsługuje paginację i wyszukiwanie po imieniu i nazwisku pracownika.

---

## ⚙ **Walidatory**

### **📌 Standardowe Walidacje**
- `Assert\Length(max=10)` – Sprawdza, czy numer VAT ma maksymalnie 10 znaków.
- `Assert\NotBlank` – Zapewnia, że pole nie jest puste.

### **📌 Customowe Walidatory**
- **`UniqueVatValidator`**
   - Sprawdza, czy numer VAT jest unikalny.
- **`UniqueEmailValidator`**
   - Sprawdza, czy email jest unikalny.

---

## 🚨 **Obsługa Wyjątków (Custom Exceptions)**
- **`InvalidFieldsException`** – Błąd (400), z podanymi polami oraz ich opisami, umożliwia łatwą obsługę błędu po stronie frontendu. 

---

## 🏭 **Wykorzystanie Wzorców Projektowych**

### **🔹 Fabryki (Factory)**
- **`CompanyFactory`** – Tworzy nowe obiekty `Company`.
- **`EmployeeFactory`** – Tworzy nowe obiekty `Employee`.

### **🔹 Repozytoria (Repository)**
- **`CompanyRepository`** – Zapewnia interakcję z bazą danych dla encji `Company`.
- **`EmployeeRepository`** – Obsługuje zapytania do bazy dla encji `Employee`.

---

## 🛠 **Testy (PHPUnit)**

### **📌 Pokrycie Testami**
- **Testy funkcjonalne** dla całego procesu dodawania, edycji i usuwania firm i pracowników.

---

## Instrukcja uruchomienia

1. **Wejdź na https://sirdomin.github.io/company-app-frontend/**

## Uruchomienie lokalnie

1. **Zbudowanie obrazu i uruchomienie kontenerów**
   ```bash
    docker-compose up -d --build
   ```
2. **Migracje i fixtury**
   ```bash
   docker-compose exec php bash
   php bin/console doctrine:migrations:migrate
   php bin/console doctrine:fixtures:load
   ```
3. **Testy**
   ```bash
   docker-compose exec php bash
   php bin/phpunit
   ```