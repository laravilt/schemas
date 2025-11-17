![Screenshot](https://raw.githubusercontent.com/laravilt/schemas/master/arts/cover.jpg)

# Laravilt Schemas

[![Latest Version](https://img.shields.io/packagist/v/laravilt/schemas.svg)](https://packagist.org/packages/laravilt/schemas)
[![Total Downloads](https://img.shields.io/packagist/dt/laravilt/schemas.svg)](https://packagist.org/packages/laravilt/schemas)
[![License](https://img.shields.io/packagist/l/laravilt/schemas.svg)](https://packagist.org/packages/laravilt/schemas)

Layout and organizational components for Laravilt. Provides a powerful schema system with Grid, Section, Tabs, Wizard, and more for organizing forms, tables, and other component containers.

## Features

- **📐 Base Schema Class** - Organize components with flexible schemas
- **🎯 Grid Layout** - Responsive grid system with column span support
- **📦 Section Component** - Visual sections with headings, descriptions, and collapsible content
- **📋 Fieldset Component** - HTML fieldset with legend support
- **🗂️ Tabs Component** - Tabbed interface with query string persistence
- **🪄 Wizard Component** - Multi-step wizard with navigation controls
- **↔️ Split Layout** - Two-column split layout
- **📏 Column Wrapper** - Column span control for grid items
- **🌍 RTL Support** - First-class right-to-left language support
- **🎨 Theme System** - Light/dark mode support
- **🧩 Fully Composable** - Nest schemas within schemas
- **🎭 Vue 3 Components** - Complete Vue 3 implementation with Composition API

## Requirements

- PHP 8.3 or 8.4
- Laravel 12.0 or higher
- Laravilt Support package
- Vue 3.4 (for frontend components)

## Installation

Install via Composer:

```bash
composer require laravilt/schemas
```

The package will be auto-discovered by Laravel.

### Publish Assets

Publish the compiled JavaScript and CSS assets:

```bash
php artisan vendor:publish --tag="laravilt-schemas-assets"
```

### Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag="laravilt-schemas-config"
```

### Publish Translations (Optional)

```bash
php artisan vendor:publish --tag="laravilt-schemas-translations"
```

## Quick Start

### Grid Layout

Create responsive grid layouts with automatic column management:

```php
use Laravilt\Schemas\Components\Grid;

$grid = Grid::make('user-grid')
    ->columns([
        'default' => 1,
        'md' => 2,
        'lg' => 3,
    ])
    ->schema([
        TextField::make('name')->columnSpan(2),
        TextField::make('email'),
        TextField::make('phone'),
    ]);
```

### Section with Collapsible Content

```php
use Laravilt\Schemas\Components\Section;

$section = Section::make('personal-info')
    ->heading('Personal Information')
    ->description('Enter your personal details')
    ->icon('<svg>...</svg>')
    ->collapsible()
    ->collapsed(false)
    ->schema([
        TextField::make('first_name'),
        TextField::make('last_name'),
        TextField::make('email'),
    ]);
```

### Tabs Interface

```php
use Laravilt\Schemas\Components\Tabs;
use Laravilt\Schemas\Components\Tab;

$tabs = Tabs::make('profile-tabs')
    ->activeTab(0)
    ->persistTabInQueryString()
    ->tabs([
        Tab::make('profile')
            ->label('Profile')
            ->icon('<svg>...</svg>')
            ->badge('3')
            ->schema([
                // Profile fields
            ]),
        Tab::make('security')
            ->label('Security')
            ->schema([
                // Security fields
            ]),
    ]);
```

### Multi-Step Wizard

```php
use Laravilt\Schemas\Components\Wizard;
use Laravilt\Schemas\Components\Step;

$wizard = Wizard::make('onboarding-wizard')
    ->skippable()
    ->submitButtonLabel('Complete')
    ->steps([
        Step::make('account')
            ->label('Account Setup')
            ->description('Create your account')
            ->icon('<svg>...</svg>')
            ->schema([
                // Account fields
            ]),
        Step::make('profile')
            ->label('Profile')
            ->schema([
                // Profile fields
            ]),
    ]);
```

### Split Layout

```php
use Laravilt\Schemas\Components\Split;

$split = Split::make('settings-split')
    ->fromBreakpoint('lg')
    ->leftSchema([
        Section::make('general')->schema([...]),
    ])
    ->rightSchema([
        Section::make('advanced')->schema([...]),
    ]);
```

## Core Concepts

### Schema System

The Schema class provides a foundation for organizing components:

```php
use Laravilt\Schemas\Schema;

$schema = Schema::make('my-schema')
    ->schema([
        Grid::make('grid')->schema([...]),
        Section::make('section')->schema([...]),
        Tabs::make('tabs')->tabs([...]),
    ]);

// Get visible components
$visible = $schema->getVisibleComponents();

// Serialize for frontend
$props = $schema->toLaraviltProps();
```

### Responsive Grid System

The Grid component supports responsive column configuration:

```php
Grid::make('responsive-grid')
    ->columns([
        'default' => 1,  // Mobile: 1 column
        'sm' => 2,       // Small: 2 columns
        'md' => 3,       // Medium: 3 columns
        'lg' => 4,       // Large: 4 columns
        'xl' => 6,       // Extra large: 6 columns
    ])
    ->schema([
        // Components can span multiple columns
        TextField::make('title')->columnSpanFull(),
        TextField::make('subtitle')->columnSpan(2),
        TextField::make('status'),
    ]);
```

### Column Spanning

Components can span multiple columns in a grid:

```php
TextField::make('description')
    ->columnSpan(2)  // Spans 2 columns
    ->columnSpanFull(); // Spans all columns

// Responsive column spans
TextField::make('featured')
    ->columnSpan([
        'default' => 1,
        'md' => 2,
        'lg' => 3,
    ]);
```

### Collapsible Sections

Sections can be collapsible with state management:

```php
Section::make('advanced-settings')
    ->heading('Advanced Settings')
    ->collapsible()
    ->collapsed(true)  // Start collapsed
    ->schema([
        // Advanced options
    ]);
```

### Tab Query String Persistence

Tabs can persist their active state in the URL:

```php
Tabs::make('user-tabs')
    ->persistTabInQueryString()  // Saves active tab in ?tab=0
    ->tabs([...]);
```

## Components Reference

### Grid

Responsive grid layout with automatic column management.

**Methods:**
- `columns(int|array $columns)` - Set column configuration
- `schema(array $components)` - Set grid items
- `getColumns()` - Get column configuration
- `getSchema()` - Get grid items

### Section

Visual section with heading, description, icon, and collapsible content.

**Methods:**
- `heading(string|Closure $heading)` - Set heading text
- `description(string|Closure $description)` - Set description
- `icon(string|Closure $icon)` - Set icon HTML
- `collapsible(bool $condition = true)` - Make collapsible
- `collapsed(bool $collapsed = true)` - Set initial collapsed state
- `schema(array $components)` - Set section content

### Fieldset

HTML fieldset with legend support.

**Methods:**
- `legend(string|Closure $legend)` - Set legend text
- `schema(array $components)` - Set fieldset content

### Tabs

Tabbed interface with multiple tabs.

**Methods:**
- `tabs(array $tabs)` - Set array of Tab components
- `activeTab(int $index)` - Set initially active tab
- `persistTabInQueryString(bool $condition = true)` - Persist in URL

### Tab

Single tab within a Tabs component.

**Methods:**
- `label(string|Closure $label)` - Set tab label
- `icon(string|Closure $icon)` - Set tab icon
- `badge(string|Closure $badge)` - Set badge text
- `schema(array $components)` - Set tab content

### Wizard

Multi-step wizard with navigation.

**Methods:**
- `steps(array $steps)` - Set array of Step components
- `skippable(bool $condition = true)` - Allow skipping steps
- `submitButtonLabel(string|Closure $label)` - Customize submit button
- `nextButtonLabel(string|Closure $label)` - Customize next button
- `previousButtonLabel(string|Closure $label)` - Customize previous button

### Step

Single step within a Wizard component.

**Methods:**
- `label(string|Closure $label)` - Set step label
- `description(string|Closure $description)` - Set step description
- `icon(string|Closure $icon)` - Set step icon
- `schema(array $components)` - Set step content

### Split

Two-column split layout.

**Methods:**
- `fromBreakpoint(string $breakpoint)` - Breakpoint for split (sm, md, lg, xl)
- `leftSchema(array $components)` - Set left column content
- `rightSchema(array $components)` - Set right column content

### Columns

Column span wrapper for grid items.

**Methods:**
- `columnSpan(int|array $span)` - Set column span
- `schema(array $components)` - Set wrapped content

## Vue 3 Components

Install as a Vue plugin or use components individually:

```javascript
import { Grid, Section, Tabs, Wizard, Split } from '@laravilt/schemas';

const app = createApp({});
app.component('Grid', Grid);
app.component('Section', Section);
app.component('Tabs', Tabs);
app.component('Wizard', Wizard);
app.component('Split', Split);
```

## Translations

The package includes English and Arabic translations:

```php
// Wizard translations
__('laravilt-schemas::schemas.wizard.next')      // Next
__('laravilt-schemas::schemas.wizard.previous')  // Previous
__('laravilt-schemas::schemas.wizard.submit')    // Submit
__('laravilt-schemas::schemas.wizard.skip')      // Skip
```

### Supported Locales

- English (`en`)
- Arabic (`ar`) with full RTL support

## RTL Support

All components have first-class RTL support:

```php
// Set locale
app()->setLocale('ar');

// Components automatically detect RTL
$section = Section::make('test');
$props = $section->toLaraviltProps();
// $props['rtl'] === true
```

## Theme Support

Components support light/dark themes:

```php
// Set theme in session
session(['theme' => 'dark']);

// Components automatically include theme
$grid = Grid::make('test');
$props = $grid->toLaraviltProps();
// $props['theme'] === 'dark'
```

## Development

### Building Assets

```bash
cd packages/schemas

# Install dependencies
npm install

# Development build (watch mode)
npm run dev

# Production build
npm run build
```

### Testing

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage
```

### Code Quality

```bash
# Format code
composer format

# Run static analysis
composer analyse
```

## Examples

### Complex Form Layout

```php
use Laravilt\Schemas\Components\Grid;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Components\Tabs;
use Laravilt\Schemas\Components\Tab;

Grid::make('user-form')
    ->columns(['default' => 1, 'lg' => 2])
    ->schema([
        Section::make('basic-info')
            ->heading('Basic Information')
            ->columnSpanFull()
            ->schema([
                TextField::make('name'),
                TextField::make('email'),
            ]),

        Tabs::make('details')
            ->columnSpanFull()
            ->tabs([
                Tab::make('profile')
                    ->label('Profile')
                    ->schema([
                        TextArea::make('bio'),
                        DatePicker::make('birth_date'),
                    ]),
                Tab::make('settings')
                    ->label('Settings')
                    ->schema([
                        Toggle::make('notifications'),
                        Select::make('timezone'),
                    ]),
            ]),
    ]);
```

### Nested Schemas

```php
Section::make('address')
    ->heading('Address')
    ->schema([
        Grid::make('address-grid')
            ->columns(2)
            ->schema([
                TextField::make('street'),
                TextField::make('city'),
                TextField::make('state'),
                TextField::make('zip'),
            ]),
    ]);
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email info@3x1.io instead of using the issue tracker.

## Credits

- [Fady Mondy](https://github.com/fadymondy)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes.
