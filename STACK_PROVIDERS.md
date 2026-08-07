# Concept Stack → Extension ServiceProviders

Довідник: який `with*()` у stack створює capability, які `*ServiceProvider` реєструє і звідки беруться їхні constructor params.

Entry: `ConceptStack::create(): StackBuilder` → `providers()`.

---

## Зведена таблиця

| Stack | Capability | ServiceProvider(s) |
|-------|------------|-------------------|
| `addMasking()` | `masking` | `DataMaskerServiceProvider` |
| `addLogging()` | `logging` | `LoggerMonologServiceProvider` |
| `addEvents()` | `events` | `EventServiceProvider` |
| `addTelemetry()` | `telemetry` | `TelemetryServiceProvider` |
| `addCasting()` | `casting` | `CastingServiceProvider` |
| `addValidation()` | `validation` | `ValidationServiceProvider` + `FormRequestServiceProvider` |
| `addDatabase()` | `database` | `PaginationConfiguratorServiceProvider` + `DatabaseEloquentServiceProvider` |
| `addSession()` | `session` | `SessionServiceProvider` (+ `CsrfServiceProvider` якщо `withCsrf()`) |
| `addHttp()` | `http` | **core** `HttpKernelServiceProvider` + `HttpServiceProvider` |
| `addConsole()` | `console` | `ConsoleSymfonyServiceProvider` |
| `addComponents()` | `components` | `ComponentsServiceProvider` |
| `addView()` | `view` | `ViewServiceProvider` + `TwigViewServiceProvider` **або** `PlatesViewServiceProvider` |
| `addErrorHandling()` | `error-handling` | `ErrorHandlerWhoopsServiceProvider` |

`HttpKernelServiceProvider` — `Concept\Core` (не extension).

---

## `addMasking()` → `DataMaskerServiceProvider`

```php
new DataMaskerServiceProvider(
    patterns: array,      // array<string, string>
    keyPatterns: array,   // list<string>
    rules: array,         // list<class-string<DataMaskerRuleInterface>>
)
```

| Param SP | Builder method |
|----------|----------------|
| `$patterns` | `->patterns([...])` |
| `$keyPatterns` | `->keyPatterns([...])` |
| `$rules` | `->rules([...])` |

---

## `addLogging()` → `LoggerMonologServiceProvider`

```php
new LoggerMonologServiceProvider(
    handlers: array,                    // list<HandlerInterface>, обовʼязково ≥1
    channel: string = 'app',
    dataMaskerFactory: ?Closure = null, // Closure(): ?DataMaskerInterface
)
```

| Param SP | Builder method |
|----------|----------------|
| `$handlers` | `->toRotatingFile($path, $maxFiles = 7, $level?)`, `->toStderr($level?)`, `->withHandler($handler)` |
| `$channel` | `->setChannel($name)` |
| `$dataMaskerFactory` | `->withMasking()` (requires `addMasking()`) → `OptionalDependency::factory(..., DataMaskerInterface)` |

`->setLevel($name)` — лише default для `toRotatingFile` / `toStderr`, у SP не передається.

---

## `addEvents()` → `EventServiceProvider`

```php
new EventServiceProvider(
    subscriberClasses: array = [], // list<class-string<ListenerSubscriber>>
)
```

`addEvents()` завжди реєструє dispatcher, навіть якщо subscribers порожні.

| Param SP | Builder method |
|----------|----------------|
| `$subscriberClasses` | `->subscribers([...])` |

---

## `addTelemetry()` → `TelemetryServiceProvider`

```php
new TelemetryServiceProvider(); // без params
```

| Поведінка / wiring | Builder method |
|--------------------|----------------|
| увімкнути boot-логіку | `->enabled(bool)` |
| `TelemetryLogHandler` + `LogHandlerRegistry` (requires logging) | `->logs(bool)` |
| event name для log handler | `->eventName(string)` |

DB query events — лише `addDatabase()->withEmitQueryEvents()` (requires events).

---

## `addCasting()` → `CastingServiceProvider`

```php
new CastingServiceProvider(
    transformerClasses: array = [], // list<class-string>
    cacheDirectory: ?string = null,
    debug: bool = false,
)
```

| Param SP | Builder method |
|----------|----------------|
| `$transformerClasses` | `->transformers([...])` |
| `$cacheDirectory` | `->cacheDir($path)` |
| `$debug` | `->debug(bool)` |

---

## `addValidation()` → `ValidationServiceProvider` + `FormRequestServiceProvider`

```php
new ValidationServiceProvider(
    customRules: array = [],           // array<string, class-string<RuleInterface>>
    logEnabled: bool = false,
    logFilePath: string = '',
    logMaxFiles: int = 7,
    dataMaskerFactory: ?Closure = null,
)

new FormRequestServiceProvider(
    validatorFactory: Closure,         // обовʼязково
    globalExcept: array = [],
    casterFactory: ?Closure = null,
    validationLoggerFactory: ?Closure = null,
)
```

| Param SP | Builder method / stack wiring |
|----------|-------------------------------|
| `$customRules` | `->setRules([...])` |
| `$logEnabled` / `$logFilePath` / `$logMaxFiles` | `->withLogging($path, $maxFiles = 7)` |
| `$dataMaskerFactory` | завжди `OptionalDependency::factory(..., DataMaskerInterface)`; `->withMasking()` лише `require(validation, masking)` |
| `$validatorFactory` | завжди lazy `ValidatorInterface` з container |
| `$globalExcept` | `->setGlobalExcept([...])` |
| `$casterFactory` | завжди optional `CasterInterface` |
| `$validationLoggerFactory` | завжди optional `ValidationLogger` |

---

## `addDatabase()` → `PaginationConfiguratorServiceProvider` + `DatabaseEloquentServiceProvider`

```php
new PaginationConfiguratorServiceProvider(); // без params

new DatabaseEloquentServiceProvider(
    connection: array,                 // array<string, mixed>, обовʼязково
    migrationPaths: array = [],        // list<string> absolute
    migrationsTable: string = 'migrations',
    seeders: array = [],               // list<class-string>
    logEnabled: bool = false,
    logFilePath: string = '',
    logMaxFiles: int = 7,
    dataMaskerFactory: ?Closure = null,
    emitQueryEvents: bool = false,
)
```

| Param SP | Builder method |
|----------|----------------|
| `$connection` | `->setConnection([...])` |
| `$migrationPaths` | `->setMigrations([...])` |
| `$migrationsTable` | `->setMigrationsTable($name)` |
| `$seeders` | `->setSeeders([...])` |
| `$logEnabled`, `$logFilePath`, `$logMaxFiles` | `->withQueryLogging($path, $maxFiles = 7)` |
| `$dataMaskerFactory` | `->withMasking()` (requires masking) |
| `$emitQueryEvents` | `->withEmitQueryEvents()` (requires events) |

---

## `addSession()` → `SessionServiceProvider` + opt-in `CsrfServiceProvider`

```php
new SessionServiceProvider(
    sessionOptions: array,             // array<string, mixed>
    handler: SessionHandlerInterface,
)

new CsrfServiceProvider(
    sessionFactory: Closure,           // Closure(): SessionInterface
)
```

| Param SP | Builder method |
|----------|----------------|
| `$sessionOptions` | `->options([...])` |
| `$handler` | `->handler($handler)`; якщо немає — `NativeFileSessionHandler()` |
| CSRF SP | `->withCsrf()` → `sessionFactory` з container |

CSRF middleware — у routes, не в stack.

---

## `addHttp()` → `HttpKernelServiceProvider` (core) + `HttpServiceProvider`

```php
new HttpKernelServiceProvider(
    routePaths: array,                          // absolute paths
    resolvers: array = [],                      // list<ArgumentResolverInterface>
    interceptors: array = [],
    notFoundMiddleware: MiddlewareInterface|Closure|null = null,
)

new HttpServiceProvider(); // без params
```

| Param / wiring | Builder method |
|----------------|----------------|
| `$routePaths` | `->setRoutes([...])` |
| `$interceptors` | `->setInterceptors([...])` |
| `$notFoundMiddleware` | `->setNotFoundMiddleware(...)` |
| `$resolvers` | збирає stack (див. нижче) |
| FormRequest resolver | `->withFormRequests()` (requires validation) |
| Typed route resolver | `->withTypedRouteParameters()` (requires casting) |

**Порядок resolvers:**

1. `FormRequestArgumentResolver` — якщо `withFormRequests()`
2. `ServerRequestArgumentResolver` — завжди
3. `TypedRouteParameterArgumentResolver` — якщо `withTypedRouteParameters()`
4. `RouteParameterArgumentResolver` — завжди

---

## `addConsole()` → `ConsoleSymfonyServiceProvider`

```php
new ConsoleSymfonyServiceProvider(
    appName: string,
    appVersion: string,
    commands: array, // list<class-string<Command>>
)
```

| Param SP | Builder method |
|----------|----------------|
| `$appName` | `->setName($name)` |
| `$appVersion` | `->setVersion($version)` |
| `$commands` | `->setCommands([...])` |

DB/View CLI commands **не** додаються автоматично — передавай у `setCommands([...])`.

---

## `addView()` → `ViewServiceProvider` + engine (requires `http`)

```php
new ViewServiceProvider(
    responseFactoryFactory: Closure,
    viewFactory: Closure,
    requestContextFactory: Closure,
    paths: array = [],            // array<string, string>
    extensions: array = [],       // list<class-string>
    routeNamespace: array = [],   // array<string, string>
)

new TwigViewServiceProvider(
    viewsPath: string,
    cacheDir: ?string = null,   // null → no filesystem cache
    debug: bool = false,
    defaultExtension: string = '.twig',
)

new PlatesViewServiceProvider(
    viewsPath: string,
    defaultExtension: string = '.php',
)
```

XOR: лише один engine — `withTwig()` **або** `withPlates()`.

| Param SP | Builder method |
|----------|----------------|
| `$paths` | `->setPaths([...])` |
| `$extensions` | `->setExtensions([...])` |
| `$routeNamespace` | `->setRouteNamespaces([...])` |
| factories Response/View/RequestContext | завжди з container |
| Twig `$viewsPath` / `$cacheDir` / `$debug` / `$defaultExtension` | `->withTwig()->setViewsPath()->setCacheDir()->setDebug()->setDefaultExtension()` |
| Plates `$viewsPath` / `$defaultExtension` | `->withPlates()->setViewsPath()->setDefaultExtension()` |

---

## `addErrorHandling()` → `ErrorHandlerWhoopsServiceProvider`

```php
new ErrorHandlerWhoopsServiceProvider(
    exceptionReporterFactory: Closure,           // Closure(): ExceptionReporterInterface
    httpErrorRendererFactory: Closure,           // Closure(): HttpErrorRendererInterface
    debugHttpHandlerFactory: ?Closure = null,    // Closure(): HandlerInterface|null — null → prod HTTP render
)
```

Реєструє в container: `ExceptionReporterInterface`, `HttpErrorRendererInterface`. Будує Whoops awake chain (report → PlainText | debug | render).

| Param SP | Builder method |
|----------|----------------|
| `$exceptionReporterFactory` | `->setLogReporting(logger: true, phpErrorLog: true)` / `->setReporter(...)` |
| `$httpErrorRendererFactory` | `->withViewRenderer($path = '')` / `->withJsonRenderer()` / `->setRenderer(...)` |
| `$debugHttpHandlerFactory` | stack: `->setDebug(true, new PrettyPageHandler())` + `!expectsJson` → handler або null |

Три осі builder: `setDebug` / `setLogReporting`+`setReporter` / `set*Renderer`.

---

## `addComponents()` → `ComponentsServiceProvider`

```php
$stack->addComponents()
    ->setClasses([BlogComponent::class])
    ->withDatabase() // seeders + migrations
    ->withConsole()  // component commands
    ->withHttp()     // component routes
    ->withView();    // view paths/extensions/namespaces
```

Усі інтеграції opt-in. `setClasses()` отримує готовий список class-string; stack не читає Config.

---

## Залежності capabilities (`require`)

| Виклик | Requires |
|--------|----------|
| `addLogging()->withMasking()` | `logging` → `masking` |
| `addTelemetry()->setLogs(true)` | `telemetry` → `logging` |
| `addDatabase()->withMasking()` | `database` → `masking` |
| `addDatabase()->withEmitQueryEvents()` | `database` → `events` |
| `addValidation()->withMasking()` | `validation` → `masking` |
| `addHttp()->withFormRequests()` | `http` → `validation` |
| `addHttp()->withTypedRouteParameters()` | `http` → `casting` |
| `addView()` | `view` → `http` (при реєстрації) |
| `addComponents()->withDatabase()` | `components` → `database` |
| `addComponents()->withConsole()` | `components` → `console` |
| `addComponents()->withHttp()` | `components` → `http` |
| `addComponents()->withView()` | `components` → `view` |
| `addErrorHandling()->setLogReporting(logger: true)` | `error-handling` → `logging` |
| `addErrorHandling()->withViewRenderer(...)` | `error-handling` → `view` |
| `addErrorHandling()->withJsonRenderer()` | `error-handling` → `http` |

Перевірка на `providers()`. Capability providers повертаються у стабільному
топологічному порядку: dependencies завжди перед consumers незалежно від порядку `addX()`.

---

## Escape hatches

| Method | Роль |
|--------|------|
| `addProvider(ServiceProviderInterface)` | app-specific provider поза capabilities |
| `require($capability, $dependency)` | ручна залежність |
| `hasCapability($name)` | чи увімкнено |
| `providers()` | validate + список SP |

---

*З `/var/www/concept-stack/src` — 2026-07-19.*
