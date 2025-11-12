# 🐘 INSTALLATION POSTGRESQL WINDOWS

## 📥 TÉLÉCHARGER POSTGRESQL :

1. **Aller sur** : https://www.postgresql.org/download/windows/
2. **Télécharger** : PostgreSQL 15.x pour Windows x64
3. **Exécuter** l'installateur

## ⚙️ CONFIGURATION INSTALLATION :

- **Port** : 5432 (par défaut)
- **Mot de passe** : `postgres` (ou votre choix)
- **Locale** : French, France
- **Composants** : Cocher pgAdmin 4

## 🔧 APRÈS INSTALLATION :

### Ajouter PostgreSQL au PATH :
1. **Ouvrir** Variables d'environnement Windows
2. **Ajouter** au PATH : `C:\Program Files\PostgreSQL\15\bin`
3. **Redémarrer** le terminal

### Tester l'installation :
```bash
psql --version
# Doit afficher : psql (PostgreSQL) 15.x
```

## 🎯 ALTERNATIVE : UTILISER XAMPP/WAMP

Si vous avez déjà XAMPP/WAMP avec MySQL :
- On peut utiliser **MySQL** au lieu de PostgreSQL
- Laravel supporte les deux
- Plus simple si déjà installé

## 🚀 SOLUTION RAPIDE : SQLite

Pour tester rapidement :
```bash
# Dans Laravel .env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```
