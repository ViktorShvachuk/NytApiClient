# NYT Best Sellers API Wrapper (Laravel)

Projekt jest implementacją wrappera dla API New York Times Best Sellers, napisaną w Laravelu.

## Struktura Katalogów
- `app/Services/Nyt/`: Klient i Data Objects.
- `app/Exceptions/`: Dedykowany wyjątek `NytApiException`.
- `config/nyt.php`: Konfiguracja API (klucze i URL).
- `resources/views/`: Widoki `welcome.blade.php` (Overview) oraz `list.blade.php` (Szczegóły listy).
- `tests/Feature/`: Testy klienta API oraz stron WWW.

## Instalacja i Uruchomienie
1. Skopiuj `.env.example` do `.env`.
2. Uzupełnij `NYT_API_KEY` w pliku `.env`.
3. Uruchom `composer install`.
4. Uruchom serwer: `php artisan serve`.

## Testy
Wszystkie testy (Feature i Integration) można uruchomić komendą:
```bash
php artisan test
```
Testy używają `Http::fake()`, więc nie wymagają aktywnego połączenia z API NYT.
