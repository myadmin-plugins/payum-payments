---
name: plugin-hook-registration
description: Codifies the src/Plugin.php static method + GenericEvent pattern for MyAdmin plugin integration. Use when user says 'add hook', 'register event', 'plugin method', 'getMenu', 'getRequirements', 'getSettings', or modifies src/Plugin.php. Covers GenericEvent->getSubject(), has_acl(), add_page_requirement(), function_requirements(). Do NOT use for gateway capture/prepare flows or Payum builder config.
---
# Plugin Hook Registration

## Critical

- All handler methods MUST be `public static` and accept exactly one `GenericEvent $event` parameter — PHPUnit tests reflect on signatures and will fail otherwise.
- `$type` MUST remain `'plugin'` — test `testTypePropertyIsPlugin` asserts this exact value.
- Hook callbacks in `getHooks()` MUST use `[__CLASS__, 'methodName']` format — never closures or instance methods.
- Known hook names are `'system.settings'`, `'ui.menu'`, and `'function.requirements'` — use only these keys.
- Call `function_requirements('has_acl')` BEFORE any `has_acl()` call inside `getMenu()` — the function may not be loaded otherwise.
- Path strings in `add_page_requirement()` must be relative to webroot using the `/../vendor/detain/...` prefix pattern.

## Instructions

1. **Verify the four required static properties exist** at the top of `src/Plugin.php`:
   ```php
   public static $name = 'Your Plugin Name';
   public static $description = 'What this plugin does';
   public static $help = '';
   public static $type = 'plugin';
   ```
   Verify all four are `public static` before proceeding.

2. **Register hooks in `getHooks()`** by adding the event name → callback mapping. Uncomment or add entries:
   ```php
   public static function getHooks()
   {
       return [
           'system.settings'      => [__CLASS__, 'getSettings'],
           'ui.menu'              => [__CLASS__, 'getMenu'],
           'function.requirements' => [__CLASS__, 'getRequirements'],
       ];
   }
   ```
   Only register hooks whose handler methods exist on the class. Verify the method names match exactly.

3. **Implement `getMenu()`** to add admin menu items with ACL guard:
   ```php
   public static function getMenu(GenericEvent $event)
   {
       $menu = $event->getSubject();
       if ($GLOBALS['tf']->ima == 'admin') {
           function_requirements('has_acl');
           if (has_acl('client_billing')) {
               // $menu->add(...) calls go here
           }
       }
   }
   ```
   The `$menu` object comes from `$event->getSubject()`. Only act when `$GLOBALS['tf']->ima == 'admin'`.

4. **Implement `getRequirements()`** to register page PHP files with the loader:
   ```php
   public static function getRequirements(GenericEvent $event)
   {
       /** @var \MyAdmin\Plugins\Loader $loader */
       $loader = $event->getSubject();
       $loader->add_page_requirement(
           'page_slug',
           '/../vendor/detain/myadmin-payum-payments/src/page_slug.php'
       );
   }
   ```
   The first argument is the page slug (no `.php`). The second is the path relative to webroot starting with `/../vendor/detain/myadmin-payum-payments/src/`. This step uses `$loader` from Step 4's `$event->getSubject()`.

5. **Implement `getSettings()`** to receive the settings object:
   ```php
   public static function getSettings(GenericEvent $event)
   {
       /** @var \MyAdmin\Settings $settings */
       $settings = $event->getSubject();
       // call $settings->add_text_setting(), $settings->add_select_setting(), etc.
   }
   ```
   If no settings are needed, keep the method body as just `$settings = $event->getSubject();` — the test `testGetSettingsExtractsSubjectFromEvent` calls this method and expects no exception.

6. **Run tests** to validate the structure:
   ```bash
   vendor/bin/phpunit tests/PluginTest.php
   ```
   All `PluginTest` cases must pass before considering the work done.

## Examples

**User says:** "Add a hook to register the payum_configure page requirement and a billing menu item"

**Actions taken:**
1. Open `src/Plugin.php`, uncomment `'ui.menu'` and `'function.requirements'` entries in `getHooks()`.
2. In `getRequirements()`, add:
   ```php
   $loader->add_page_requirement(
       'payum_configure',
       '/../vendor/detain/myadmin-payum-payments/src/payum_configure.php'
   );
   ```
3. In `getMenu()`, inside the `has_acl('client_billing')` block, add the menu entry.
4. Run `vendor/bin/phpunit tests/PluginTest.php`.

**Result:** `getHooks()` returns both hooks; `getRequirements` test verifies `add_page_requirement` was called with `'payum_configure'` and a path containing `payum_configure.php`.

## Common Issues

- **`testGetHooksValuesAreValidCallableFormat` fails**: A hook callback references a method that doesn't exist on `Plugin`. Fix: ensure every method name in `getHooks()` values has a matching `public static` method on the class.

- **`testTypePropertyIsPlugin` fails**: `$type` was changed to something other than `'plugin'`. Fix: reset to `public static $type = 'plugin';`.

- **`testGetRequirementsCallsAddPageRequirement` fails with "should have been called"**: `getHooks()` is missing the `'function.requirements'` entry, so the method was never wired. Fix: add `'function.requirements' => [__CLASS__, 'getRequirements']` to the returned array.

- **`has_acl()` call throws "function not found"**: `function_requirements('has_acl')` was not called before `has_acl()`. Fix: always call `function_requirements('has_acl')` immediately before the `has_acl()` guard inside `getMenu()`.

- **`add_page_requirement` path not found at runtime**: Path does not start with `/../vendor/detain/myadmin-payum-payments/src/`. Fix: use exactly that prefix — not an absolute path, not `./vendor/`.
