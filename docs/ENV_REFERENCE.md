# Référence des variables d'environnement

## Variables cœur Laravel
- `APP_NAME`: nom applicatif
- `APP_ENV`: environnement (`local`, `staging`, `production`)
- `APP_DEBUG`: debug activé/désactivé
- `APP_URL`: URL backend
- `APP_URL_FRONT_END`: URL frontend web

## Base de données
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

## CORS
- `CORS_ALLOWED_ORIGINS`: liste d'origines autorisées séparées par virgule

## Mail
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`

## Queue / cache / session
- `QUEUE_CONNECTION`
- `CACHE_DRIVER`
- `SESSION_DRIVER`

## Règles métier (statuts/roles)
- `STATUT_DISPONIBLE`, `STATUT_INDISPONIBLE`, `STATUT_ABSENT`, `STATUT_REPOS`, `STATUT_COURSE`, `STATUT_CONGE`
- `STATUT_DEMANDE_COURSE_CREEE`, `STATUT_DEMANDE_COURSE_AFFECTEE`, `STATUT_DEMANDE_COURSE_DEMARREE`, `STATUT_DEMANDE_COURSE_TERMINEE`
- `STATUT_CRITERE_ACTIF`, `STATUT_CRITERE_INACTIF`
- `ROLE_ADMIN`, `ROLE_AGENT`

## Notifications SMS (Moov)
- `MOOV_API_HOST`, `MOOV_API_PORT`, `MOOV_API_USERNAME`, `MOOV_API_PASSWORD`, `MOOV_API_KEY`, `MOOV_API_SRC`, `MOOV_API_REF_NUMBER`, `MOOV_API_URL`
- Variables de templates SMS `MOOV_MESSAGE_*`

## Sécurité
- Ne pas commiter de valeurs réelles de mots de passe/API keys.
- Utiliser des placeholders dans `.env.example`.
