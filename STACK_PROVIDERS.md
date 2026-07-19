# Concept Stack → Extension ServiceProviders

Довідник: який `with*()` у stack створює capability, які `*ServiceProvider` реєструє і звідки беруться їхні constructor params.

Entry: `ConceptStack::create(): StackBuilder` → `providers()`.

---

## Зведена таблиця

| Stack | Capability | ServiceProvider(s) |
|-------|------------|-------------------|
| `withMasking()` | `masking` | `DataMaskerServiceProvider` |
| `withLogging()` | `logging` | `LoggerMonologServiceProvider` |
| `withEvents()` | `events` | `EventServiceProvider` |
| `withTelemetry()` | `telemetry` | `TelemetryServiceProvider` |
| `withCasting()` | `casting` | `CastingServiceProvider` |
| `withValidation()` | `validation` | `ValidationServiceProvider` + `FormRequestServiceProvider` |
| `withDatabase()` | `database` | `PaginationConfiguratorServiceProvider` + `DatabaseEloquentServiceProvider` |
| `withSession()` | `session` | `SessionServiceProvider` (+ `CsrfServiceProvider` якщо `withCsrf()`) |
| `withHttp()` | `http` | **core** `HttpKernelServiceProvider` + `HttpServiceProvider` |
| `withConsole()` | `console` | `ConsoleSymfonyServiceProvider` |
| `withComponents()` | `components` | `ComponentsServiceProvider` |
| `withView()` | `view` | `ViewServiceProvider` + `TwigViewServiceProvider` **або** `PlatesViewServiceProvider` |
| `withErrorHandling()` | `error-handling` | `ErrorHandlerWhoopsServiceProvider` |

`HttpKernelServiceProvider` — `Concept\Core` (не extension).

---

## `withMasking()` → `DataMaskerServiceProvider`

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

## `withLogging()` → `LoggerMonologServiceProvider`

```php
new LoggerMonologServiceProvider(
    handlers: array,                    // list<HandlerInterface>, обовʼязково ≥1
    channel: string = 'app',
    dataMaskerFactory: ?Closure = null, // Closure(): ?DataMaskerInterface
)
```

| Param SP | Builder method |
|----------|----------------|
| `$handlers` | `->toRotatingFile($path, $maxFiles = 7, $level?)`, `->toStderr($level?)`, `->toHandler($handler)` |
| `$channel` | `->channel($name)` |
| `$dataMaskerFactory` | `->withMasking()` (requires `withMasking()`) → `OptionalDependency::factory(..., DataMaskerInterface)` |

`->level($name)` — лише default для `toRotatingFile` / `toStderr`, у SP не передається.

---

## `withEvents()` → `EventServiceProvider`

```php
new EventServiceProvider(
    subscriberClasses: array = [], // list<class-string<ListenerSubscriber>>
)
```

`withEvents()` завжди реєструє dispatcher, навіть якщо subscribers порожні.

| Param SP | Builder method |
|----------|----------------|
| `$subscriberClasses` | `->subscribers([...])` |

---

## `withTelemetry()` → `TelemetryServiceProvider`

```php
new TelemetryServiceProvider(); // без params
```

| Поведінка / wiring | Builder method |
|--------------------|----------------|
| увімкнути boot-логіку | `->enabled(bool)` |
| `TelemetryLogHandler` + `LogHandlerRegistry` (requires logging) | `->logs(bool)` |
| event name для log handler | `->eventName(string)` |

DB query events — лише `withDatabase()->withEmitQueryEvents()` (requires events).

---

## `withCasting()` → `CastingServiceProvider`

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

## `withValidation()` → `ValidationServiceProvider` + `FormRequestServiceProvider`

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
| `$customRules` | `->rules([...])` / `->customRules([...])` |
| `$logEnabled` | `->logEnabled(bool)` або `->logFile($path)` (вмикає log) |
| `$logFilePath` | `->logFile($path)` / `->logFilePath($path)` |
| `$logMaxFiles` | `->logMaxFiles(int)` |
| `$dataMaskerFactory` | завжди `OptionalDependency::factory(..., DataMaskerInterface)`; `->withMasking()` лише `require(validation, masking)` |
| `$validatorFactory` | завжди lazy `ValidatorInterface` з container |
| `$globalExcept` | `->globalExcept([...])` |
| `$casterFactory` | завжди optional `CasterInterface` |
| `$validationLoggerFactory` | завжди optional `ValidationLogger` |

---

## `withDatabase()` → `PaginationConfiguratorServiceProvider` + `DatabaseEloquentServiceProvider`

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
| `$connection` | `->connection([...])` |
| `$migrationPaths` | `->migrations([...])` |
| `$migrationsTable` | `->migrationsTable($name)` |
| `$seeders` | `->seeders([...])` |
| `$logEnabled`, `$logFilePath`, `$logMaxFiles` | `->withQueryLogging($path, $maxFiles = 7)` |
| `$dataMaskerFactory` | `->withMasking()` (requires masking) |
| `$emitQueryEvents` | `->withEmitQueryEvents()` (requires events) |

---

## `withSession()` → `SessionServiceProvider` + opt-in `CsrfServiceProvider`

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

## `withHttp()` → `HttpKernelServiceProvider` (core) + `HttpServiceProvider`

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
| `$routePaths` | `->routes([...])` |
| `$interceptors` | `->interceptors([...])` |
| `$notFoundMiddleware` | `->notFound(...)` |
| `$resolvers` | збирає stack (див. нижче) |
| FormRequest resolver | `->withFormRequests()` (requires validation) |
| Typed route resolver | `->withTypedRouteParameters()` (requires casting) |

**Порядок resolvers:**

1. `FormRequestArgumentResolver` — якщо `withFormRequests()`
2. `ServerRequestArgumentResolver` — завжди
3. `TypedRouteParameterArgumentResolver` — якщо `withTypedRouteParameters()`
4. `RouteParameterArgumentResolver` — завжди

---

## `withConsole()` → `ConsoleSymfonyServiceProvider`

```php
new ConsoleSymfonyServiceProvider(
    appName: string,
    appVersion: string,
    commands: array, // list<class-string<Command>>
)
```

| Param SP | Builder method |
|----------|----------------|
| `$appName` | `->name($name)` |
| `$appVersion` | `->version($version)` |
| `$commands` | `->commands([...])` |

DB/View CLI commands **не** додаються автоматично — передавай у `commands([...])`.

---

## `withView()` → `ViewServiceProvider` + engine (requires `http`)

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
    cacheDir: string = '',
    debug: bool = false,
)

new PlatesViewServiceProvider(
    viewsPath: string,
)
```

XOR: лише один engine — `withTwig()` **або** `withPlates()`.

| Param SP | Builder method |
|----------|----------------|
| `$paths` | `->paths([...])` |
| `$extensions` | `->extensions([...])` |
| `$routeNamespace` | `->routeNamespace([...])` |
| factories Response/View/RequestContext | завжди з container |
| Twig `$viewsPath` / `$cacheDir` / `$debug` | `->withTwig()->viewsPath()->cacheDir()->debug()` |
| Plates `$viewsPath` | `->withPlates()->viewsPath()` |

---

## `withErrorHandling()` → `ErrorHandlerWhoopsServiceProvider`

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
| `$exceptionReporterFactory` | `->reportToLog()` / `->reporter(...)` / `->exceptionReporter(fn)` |
| `$httpErrorRendererFactory` | `->renderHtmlErrorPage($path = '')` / `->renderJson()` / `->renderer(...)` |
| `$debugHttpHandlerFactory` | stack: `debug(true)` + `->showDebugExceptionPage()` + `!expectsJson` → PrettyPage або null |

Один debug closure замість `debug` + `debugHttpHandlerFactory` + `useDebugHttpHandler` — уся умова в stack.

---

## `withComponents()` → `ComponentsServiceProvider`

```php
$stack->withComponents()
    ->classes([BlogComponent::class])
    ->withDatabase() // seeders + migrations
    ->withConsole()  // component commands
    ->withHttp()     // component routes
    ->withView();    // view paths/extensions/namespaces
```

Усі інтеграції opt-in. `classes()` отримує готовий список class-string; stack не читає Config.

---

## Залежності capabilities (`require`)

| Виклик | Requires |
|--------|----------|
| `withLogging()->withMasking()` | `logging` → `masking` |
| `withTelemetry()->logs(true)` | `telemetry` → `logging` |
| `withDatabase()->withMasking()` | `database` → `masking` |
| `withDatabase()->withEmitQueryEvents()` | `database` → `events` |
| `withValidation()->withMasking()` | `validation` → `masking` |
| `withHttp()->withFormRequests()` | `http` → `validation` |
| `withHttp()->withTypedRouteParameters()` | `http` → `casting` |
| `withView()` | `view` → `http` (при реєстрації) |
| `withComponents()->withDatabase()` | `components` → `database` |
| `withComponents()->withConsole()` | `components` → `console` |
| `withComponents()->withHttp()` | `components` → `http` |
| `withComponents()->withView()` | `components` → `view` |
| `withErrorHandling()->reportToLog()` | `error-handling` → `logging` |
| `withErrorHandling()->renderHtmlErrorPage(...)` | `error-handling` → `view` |
| `withErrorHandling()->renderJson()` | `error-handling` → `http` |

Перевірка на `providers()`. Capability providers повертаються у стабільному
топологічному порядку: dependencies завжди перед consumers незалежно від порядку `withX()`.

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
