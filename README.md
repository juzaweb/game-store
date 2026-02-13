# Game Module

This module provides game management functionality for Juzaweb CMS.

## Features

- **Game Management**: Create, read, update, and delete games
- **Category Management**: Organize games into hierarchical categories
- **Multi-language Support**: All game and category content supports multiple languages
- **Media Support**: Upload and manage game thumbnails and screenshots
- **Bulk Actions**: Perform bulk operations on multiple games
- **Admin Interface**: Full admin panel integration with DataTables
- **IGDB Integration**: Import games directly from the Internet Game Database (IGDB)

## IGDB Integration

This module integrates with IGDB (Internet Game Database) to allow importing game data automatically.

### Setup

1. Get IGDB API credentials:
   - Visit [Twitch Developer Console](https://dev.twitch.tv/console/apps)
   - Create a new application
   - Copy your Client ID and Client Secret

2. Configure environment variables in `.env`:
   ```bash
   TWITCH_CLIENT_ID=your_client_id_here
   TWITCH_CLIENT_SECRET=your_client_secret_here
   ```

3. (Optional) Configure cache lifetime in `config/igdb.php`:
   ```php
   'cache_lifetime' => env('IGDB_CACHE_LIFETIME', 3600), // Cache for 1 hour
   ```

### Using IGDB Import

1. Navigate to **Games > Games** in the admin panel
2. Click the "Import from IGDB" button
3. Search for a game by name
4. Click on a game from the search results to import it

The import process will automatically:
- Create a new game with title and description from IGDB
- Download and attach the game's cover image as thumbnail
- Download up to 5 screenshots
- Import associated vendors/developers
- Import supported platforms
- Import supported languages
- Set the game status to "Draft" for review

### Customization

The IGDB import functionality is implemented in:
- Controller: `modules/GameStore/Http/Controllers/GameController.php`
  - `searchIgdb()` - Searches IGDB for games
  - `importFromIgdb()` - Imports a game from IGDB
- Routes: `modules/GameStore/routes/admin.php`
  - `POST /games/igdb/search` - Search endpoint
  - `POST /games/igdb/import` - Import endpoint
- View: `modules/GameStore/resources/views/game/partials/igdb-import-modal.blade.php`

## Models

### Game
- UUID-based primary key
- Translatable fields: title, description, slug
- Supports categories, views tracking, and status
- Media support for thumbnails

### GameCategory
- UUID-based primary key
- Translatable fields: name, description, slug
- Parent-child category hierarchy
- Related games

### GameVendor
- UUID-based primary key
- Translatable fields: name, slug
- Stores information about game publishers, studios, or vendors

### GamePlatform
- UUID-based primary key
- Translatable fields: name, slug
- Stores supported gaming platforms (e.g., PC, PlayStation, Xbox, Nintendo Switch)

### GameLanguage
- UUID-based primary key
- Translatable fields: name, slug
- Stores supported in-game languages

## Installation

The module is automatically loaded by Juzaweb's module system. After installation:

1. Run migrations:
   ```bash
   php artisan migrate
   ```

2. The module will automatically register routes and admin menus

## Usage

### Admin Panel

Access the game management interface through the admin panel:
- Navigate to **Games > Games** to manage games
- Navigate to **Games > Categories** to manage game categories

### Permissions

The module uses the following permissions:
- `games.index` - View games list
- `games.create` - Create new games
- `games.edit` - Edit existing games
- `games.delete` - Delete games
- `game-categories.index` - View categories list
- `game-categories.create` - Create new categories
- `game-categories.edit` - Edit existing categories
- `game-categories.delete` - Delete categories

## API

The module follows Juzaweb's standard routing conventions:
- Admin routes are prefixed with `/admin-cp/{website_id}/`
- All routes are protected by admin middleware and permissions
