# Endpoints API ParcAuto

## Public
- `POST /api/login` -> authentification
- `POST /api/forgot-password` -> demande de réinitialisation
- `POST /api/reset-password` -> confirmation réinitialisation

## Protégés (`auth:sanctum`)

### Auth
- `GET /api/auth/profile`
- `POST /api/logout`

### Dashboard
- `GET /api/dashboard/stats`

### Users
- `GET /api/user/all`
- `GET /api/user/get/{userId}`
- `POST /api/user/update`
- `POST /api/user/delete`
- `POST /api/user/changePassword`

### Véhicules
- `GET /api/vehicule/list`
- `GET /api/vehicule/type`
- `GET /api/vehicule/get/{vehiculeId}`
- `POST /api/vehicule/save`

### Demandes de course
- `POST /api/demande/save`
- `GET /api/demande/list/{user_id}/{role}`
- `GET /api/demande/get/{demandeId}`
- `POST /api/demande/edit`
- `POST /api/demande/delete`
- `POST /api/demande/start/{demandeId}`
- `POST /api/demande/close/{demandeId}`
- `GET /api/demande/en-cours/{user_id}/{role}`
- `GET /api/demande/notation-check/{user_id}`

### Affectations
- `GET /api/affectation/list`
- `POST /api/affectation/save`

### Entités
- `GET /api/entites`
- `POST /api/entites`
- `GET /api/entites/{entite}`
- `PUT/PATCH /api/entites/{entite}`
- `DELETE /api/entites/{entite}`
