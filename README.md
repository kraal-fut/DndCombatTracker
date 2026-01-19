# D&D Combat Tracker

A comprehensive web-based combat tracker for Dungeons & Dragons 5th Edition built with Laravel 12 and Tailwind CSS 4.

## Features

### Initiative Tracker
- Add characters with initiative rolls
- Automatic sorting by initiative (highest to lowest)
- Visual indicator for current turn
- Track player characters vs NPCs
- Character stats (HP, AC, Initiative)
- **Quick HP Management**:
  - Inline damage/heal input fields next to HP display
  - Red button (-) for dealing damage
  - Green button (+) for healing
  - HP automatically capped at 0 (minimum) and max HP (maximum)
  - Color-coded HP display:
    - Green: 70-100% HP (Healthy)
    - Yellow: 31-70% HP (Injured)
    - Red: 30% HP and below (Low HP)

### Combat Management
- Create multiple combat encounters
- **Dynamic Turn Order**: When a character's turn ends, they move to the bottom of the initiative list
  - First character in the list is always the current turn
  - No cycling through indices - the list reorders dynamically
  - Reactions reset automatically when a character's turn ends
  - Conditions and state effects decrement their duration when the character's turn ends
  - **Automatic Round Tracking**: The round counter increments when the character with the lowest initiative completes their turn
    - Works correctly even with tied initiatives (multiple characters sharing the same initiative value)
    - Detects round completion by checking if the next character has a higher initiative than the current one
- Advance to next turn (moves current character to bottom)
- Advance to next round (resets all character positions)
- Pause/Resume/End combat
- Remove individual characters or all at once

### Conditions Tracker
- Track all standard D&D 5e conditions:
  - Blinded, Charmed, Deafened, Frightened
  - Grappled, Incapacitated, Invisible
  - Paralyzed, Petrified, Poisoned
  - Prone, Restrained, Stunned
  - Unconscious, Exhaustion, Concentration
- Add custom conditions with descriptions
- Optional duration tracking (auto-decrements each round)
- Color-coded condition badges

### State Effects Tracker
- Track bonuses and penalties
- Support for advantage/disadvantage states
- Custom effect names and descriptions
- Optional duration tracking
- Visual indicators for effect types

### Reactions Tracker
- Add character reactions (e.g., Attack of Opportunity, Shield)
- Mark reactions as used
- **D&D 5e Rule Enforcement**: Only one reaction can be used per round
  - Once a reaction is used, all other reactions become unavailable
  - Visual indicators show unavailable reactions (grayed out with tooltip)
  - Attempting to use a second reaction shows an error message
- Automatic reset at the start of each round
- Visual indicators for used/unused reactions

## Installation

### Requirements
- PHP 8.2 or higher
- Composer
- SQLite (default) or other database
- Node.js 20.19+ or 22.12+ (for asset building)

### Setup

1. Clone the repository:
```bash
git clone <repository-url>
cd DndCombatTracker
```

2. Install PHP dependencies:
```bash
composer install
```

3. Create environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run migrations:
```bash
php artisan migrate
```

6. Install Node dependencies and build assets:
```bash
npm install
npm run build
```

7. Start the development server:
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Testing

This project uses Pest for testing with comprehensive test coverage.

Run all tests:
```bash
php artisan test
```

Run specific test file:
```bash
php artisan test --filter=CombatServiceTest
```

### Test Coverage

- **CombatServiceTest**: Tests for combat creation, character management, turn/round advancement
- **CombatControllerTest**: Tests for HTTP routes and responses
- **CharacterConditionTest**: Tests for condition tracking and duration management
- **CharacterStateEffectTest**: Tests for state effects (bonuses/penalties/advantage)
- **CharacterReactionTest**: Tests for reaction tracking and reset functionality

## Usage Guide

### Creating a Combat

1. Click "New Combat" in the navigation
2. Enter a name for your combat encounter
3. Click "Create Combat"

### Adding Characters

1. From the combat view, click "Add Character"
2. Enter character details:
   - Name (required)
   - Initiative (required)
   - Max HP, Current HP (optional)
   - Armor Class (optional)
   - Mark as Player Character (optional)
3. Click "Add Character"

Characters are automatically sorted by initiative.

### Managing Combat Flow

- **Next Turn**: Advances to the next character in initiative order
- **Next Round**: Skips to the start of the next round and resets all reactions
- **Pause/Resume**: Temporarily pause combat tracking
- **End Combat**: Mark the combat as completed

### Adding Conditions

1. Find the character in the initiative list
2. In their Conditions section, click "Add"
3. Select a condition type (or choose "Custom" for homebrew conditions)
4. Optionally add a duration in rounds
5. Click "Add Condition"

Conditions with duration will automatically decrement each round and be removed when they reach 0.

### Adding State Effects

1. Find the character in the initiative list
2. In their State Effects section, click "Add"
3. Enter effect details:
   - Name (e.g., "Bless", "Bane")
   - Modifier Type (Bonus or Penalty)
   - Value (numeric bonus/penalty)
   - Advantage State (Normal, Advantage, Disadvantage)
4. Optionally add a duration in rounds
5. Click "Add State Effect"

### Adding Reactions

1. Find the character in the initiative list
2. In their Reactions section, click "Add"
3. Enter reaction name and optional description
4. Click "Add Reaction"

Use the checkmark (✓) to mark a reaction as used, or the reset button (↺) to mark it as available again.

## Architecture

### Models

- **Combat**: Represents a combat encounter
- **CombatCharacter**: A character participating in combat
- **CharacterCondition**: Tracks status conditions on characters
- **CharacterStateEffect**: Tracks bonuses/penalties/advantage states
- **CharacterReaction**: Tracks character reactions

### Enums

- **CombatStatus**: Active, Paused, Completed
- **ConditionType**: All D&D 5e conditions plus Custom
- **StateModifierType**: Bonus, Penalty
- **AdvantageState**: Normal, Advantage, Disadvantage

### Services

- **CombatService**: Business logic for combat management

### Data Transfer Objects (DTOs)

- **AddCharacterData**: Encapsulates character creation data, avoiding functions with multiple parameters

### Controllers

- **CombatController**: Main combat CRUD operations
- **CombatCharacterController**: Character management
- **CharacterConditionController**: Condition management
- **CharacterStateEffectController**: State effect management
- **CharacterReactionController**: Reaction management

## Development

### Code Style

This project follows Laravel best practices and the rules defined in `.cursor/rules/laravel.mdc`.

### Adding New Features

When adding new features:

1. Create migrations for database changes
2. Update models with relationships and casts
3. Add service methods for business logic
4. Create controllers for HTTP handling
5. Add routes in `routes/web.php`
6. Create Blade views
7. Write Pest tests for all functionality

## License

This project is open-source software.

## Credits

Built with:
- [Laravel 12](https://laravel.com)
- [Tailwind CSS 4](https://tailwindcss.com)
- [Pest PHP](https://pestphp.com)
