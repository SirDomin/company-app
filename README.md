# Symfony Book Management Project

## 📌 Opis

- **Symfony**: 7.1.3
- **PHP**: 8.3.10
- **Baza danych**: PostgreSQL
- **Docker**: Tak
- **API**: Pełny REST API
- **Frontend**: [GitHub Pages](https://sirdomin.github.io/company-app-frontend/)
- **Hosting**: Mój serwer

---

## 📌 Funkcjonalności

### **1. Zarządzanie Firmami** ([CompanyController](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php))

#### 🔹 **Operacje na firmach**
- **Dodawanie Firmy** ([create_company](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php#L24))
  - Walidacja danych wejściowych.
  - Sprawdzenie unikalności numeru VAT (`UniqueVatValidator`).
  - Tworzenie firmy (`CompanyFactory`).
  - Zapis do bazy (`CompanyRepository`).
- **Edycja Firmy** ([update_company](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php#L85))
  - Pobranie i aktualizacja danych firmy.
- **Usuwanie Firmy** ([delete_company](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php#L100))
  - Usunięcie firmy z bazy.
- **Lista Firm** ([get_companies](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php#L59))
  - Paginacja i wyszukiwanie po nazwie oraz NIP.

### **2. Zarządzanie Pracownikami** ([EmployeeController](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php))

#### 🔹 **Operacje na pracownikach**
- **Dodawanie Pracownika** ([create_employee](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php#L24))
  - Walidacja danych.
  - Sprawdzenie istnienia firmy.
  - Tworzenie pracownika (`EmployeeFactory`).
  - Zapis do bazy (`EmployeeRepository`).
- **Edycja Pracownika** ([update_employee](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php#L85))
  - Pobranie i aktualizacja danych pracownika.
- **Usuwanie Pracownika** ([delete_employee](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php#L107))
  - Usunięcie pracownika z bazy.
- **Lista Pracowników** ([get_employee](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php#L59))
  - Paginacja i wyszukiwanie po imieniu i nazwisku.

---

## ⚙ **Walidatory**

### **📌 Standardowe Walidacje**
- `Assert\Length(max=10)` – Numer VAT (maks. 10 znaków).
- `Assert\NotBlank` – Pole nie może być puste.

### **📌 Customowe Walidatory**
- **[UniqueVatValidator](https://github.com/SirDomin/company-app/blob/master/src/Validator/UniqueVatValidator.php)** – Sprawdza unikalność numeru VAT.
- **[UniqueEmailValidator](https://github.com/SirDomin/company-app/blob/master/src/Validator/UniqueEmailValidator.php)** – Sprawdza unikalność emaila.

---

## 🚨 **Obsługa Wyjątków**
- **[InvalidFieldsException](https://github.com/SirDomin/company-app/blob/master/src/Exception/InvalidFieldsException.php)** – Błąd (400) z podanymi polami i opisami do obsługi na frontendzie.

---

## 🏭 **Wzorce Projektowe**

### **🔹 Fabryki (Factory)**
- **[CompanyFactory](https://github.com/SirDomin/company-app/blob/master/src/Factory/CompanyFactory.php)** – Tworzy obiekty [Company](https://github.com/SirDomin/company-app/blob/master/src/Entity/Company.php).
- **[EmployeeFactory](https://github.com/SirDomin/company-app/blob/master/src/Factory/EmployeeFactory.php)** – Tworzy obiekty [Employee](https://github.com/SirDomin/company-app/blob/master/src/Entity/Employee.php).

### **🔹 Repozytoria (Repository)**
- **[CompanyRepository](https://github.com/SirDomin/company-app/blob/master/src/Repository/CompanyRepository.php)** – Operacje na bazie dla encji [Company](https://github.com/SirDomin/company-app/blob/master/src/Entity/Company.php).
- **[EmployeeRepository](https://github.com/SirDomin/company-app/blob/master/src/Repository/EmployeeRepository.php)** – Operacje na bazie dla encji [Employee](https://github.com/SirDomin/company-app/blob/master/src/Entity/Employee.php).

---

## 🛠 **Testy (PHPUnit)**

### **📌 Pokrycie Testami**
- **Testy funkcjonalne**: dodawanie, edycja, usuwanie firm i pracowników.
  - **[CompanyTest](https://github.com/SirDomin/company-app/blob/master/tests/App/Tests/CompanyTest.php)** – Testy CRUD dla [CompanyController](https://github.com/SirDomin/company-app/blob/master/src/Controller/CompanyController.php).
  - **[EmployeeTest](https://github.com/SirDomin/company-app/blob/master/tests/App/Tests/EmployeeTest.php)** – Testy CRUD dla [EmployeeController](https://github.com/SirDomin/company-app/blob/master/src/Controller/EmployeeController.php).

---

## 📌 **Instrukcja uruchomienia**

### ✅ **Dostęp online**
- Wejdź na: [Company App Frontend](https://sirdomin.github.io/company-app-frontend/)

### ✅ **Uruchomienie lokalne**

#### 1️⃣ **Zbudowanie obrazu i uruchomienie kontenerów**
```bash
docker-compose up -d --build
```

#### 2️⃣ **Migracje i fixtury**
```bash
docker-compose exec php bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

#### 3️⃣ **Testy**
```bash
docker-compose exec php bash
php bin/phpunit
```

