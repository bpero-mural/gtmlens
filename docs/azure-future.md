# Future Azure

Azure is not used in the MVP.

The code should remain ready for a later deployment to:

- Azure Container Apps for web, worker, and scheduled commands.
- Azure Database for PostgreSQL Flexible Server.
- Azure Blob Storage via Laravel filesystem disks.
- Azure Key Vault for secrets.
- Microsoft Entra ID for SSO.
- Azure Container Registry.
- Sanitized Azure monitoring/logging.

Domain code should not depend directly on Azure services.
