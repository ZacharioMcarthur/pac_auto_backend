# Contrat API ParcAuto

## Base URL
- Local: `http://127.0.0.1:8000/api`

## Headers requis
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer <token>` pour routes protégées

## Format de réponse standard
### Succès
```json
{
  "success": true,
  "status": 200,
  "message": "Opération réussie",
  "data": {}
}
```

### Erreur de validation
```json
{
  "success": false,
  "status": 422,
  "message": "Erreur de validation",
  "errors": {
    "field": ["message"]
  }
}
```

### Erreur applicative
```json
{
  "success": false,
  "status": 500,
  "message": "Une erreur interne est survenue",
  "data": null
}
```

## Convention HTTP
- `200`: lecture/modification OK
- `201`: création OK
- `401`: non authentifié
- `403`: action interdite pour l'état métier
- `404`: ressource introuvable
- `422`: payload invalide
- `500`: erreur interne

## Pagination
Par défaut, ajouter pagination sur les endpoints de listing volumineux:
- query params recommandés: `page`, `per_page`
- renvoyer un bloc `meta` lorsque pagination active.
