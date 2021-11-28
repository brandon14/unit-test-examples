

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '<ul><li data-name="namespace:App" class="opened"><div style="padding-left:0px" class="hd"><span class="icon icon-play"></span><a href="App.html">App</a></div><div class="bd"><ul><li data-name="namespace:App_Contracts" class="opened"><div style="padding-left:18px" class="hd"><span class="icon icon-play"></span><a href="App/Contracts.html">Contracts</a></div><div class="bd"><ul><li data-name="namespace:App_Contracts_Services" ><div style="padding-left:36px" class="hd"><span class="icon icon-play"></span><a href="App/Contracts/Services.html">Services</a></div><div class="bd"><ul><li data-name="namespace:App_Contracts_Services_LastModified" ><div style="padding-left:54px" class="hd"><span class="icon icon-play"></span><a href="App/Contracts/Services/LastModified.html">LastModified</a></div><div class="bd"><ul><li data-name="class:App_Contracts_Services_LastModified_LastModifiedOptions" ><div style="padding-left:80px" class="hd leaf"><a href="App/Contracts/Services/LastModified/LastModifiedOptions.html">LastModifiedOptions</a></div></li><li data-name="class:App_Contracts_Services_LastModified_LastModifiedService" ><div style="padding-left:80px" class="hd leaf"><a href="App/Contracts/Services/LastModified/LastModifiedService.html">LastModifiedService</a></div></li><li data-name="class:App_Contracts_Services_LastModified_LastModifiedTimeProvider" ><div style="padding-left:80px" class="hd leaf"><a href="App/Contracts/Services/LastModified/LastModifiedTimeProvider.html">LastModifiedTimeProvider</a></div></li></ul></div></li><li data-name="namespace:App_Contracts_Services_Status" ><div style="padding-left:54px" class="hd"><span class="icon icon-play"></span><a href="App/Contracts/Services/Status.html">Status</a></div><div class="bd"><ul><li data-name="class:App_Contracts_Services_Status_StatusOptions" ><div style="padding-left:80px" class="hd leaf"><a href="App/Contracts/Services/Status/StatusOptions.html">StatusOptions</a></div></li><li data-name="class:App_Contracts_Services_Status_StatusService" ><div style="padding-left:80px" class="hd leaf"><a href="App/Contracts/Services/Status/StatusService.html">StatusService</a></div></li><li data-name="class:App_Contracts_Services_Status_StatusServiceProvider" ><div style="padding-left:80px" class="hd leaf"><a href="App/Contracts/Services/Status/StatusServiceProvider.html">StatusServiceProvider</a></div></li></ul></div></li><li data-name="class:App_Contracts_Services_CacheException" ><div style="padding-left:62px" class="hd leaf"><a href="App/Contracts/Services/CacheException.html">CacheException</a></div></li><li data-name="class:App_Contracts_Services_CacheImplementationNeededException" ><div style="padding-left:62px" class="hd leaf"><a href="App/Contracts/Services/CacheImplementationNeededException.html">CacheImplementationNeededException</a></div></li><li data-name="class:App_Contracts_Services_InvalidDateFormatException" ><div style="padding-left:62px" class="hd leaf"><a href="App/Contracts/Services/InvalidDateFormatException.html">InvalidDateFormatException</a></div></li><li data-name="class:App_Contracts_Services_ProviderRegistrationException" ><div style="padding-left:62px" class="hd leaf"><a href="App/Contracts/Services/ProviderRegistrationException.html">ProviderRegistrationException</a></div></li></ul></div></li></ul></div></li><li data-name="namespace:App_Services" class="opened"><div style="padding-left:18px" class="hd"><span class="icon icon-play"></span><a href="App/Services.html">Services</a></div><div class="bd"><ul><li data-name="namespace:App_Services_LastModified" ><div style="padding-left:36px" class="hd"><span class="icon icon-play"></span><a href="App/Services/LastModified.html">LastModified</a></div><div class="bd"><ul><li data-name="namespace:App_Services_LastModified_Providers" ><div style="padding-left:54px" class="hd"><span class="icon icon-play"></span><a href="App/Services/LastModified/Providers.html">Providers</a></div><div class="bd"><ul><li data-name="class:App_Services_LastModified_Providers_CacheLastModifiedTimeProvider" ><div style="padding-left:80px" class="hd leaf"><a href="App/Services/LastModified/Providers/CacheLastModifiedTimeProvider.html">CacheLastModifiedTimeProvider</a></div></li><li data-name="class:App_Services_LastModified_Providers_FilesystemLastModifiedTimeProvider" ><div style="padding-left:80px" class="hd leaf"><a href="App/Services/LastModified/Providers/FilesystemLastModifiedTimeProvider.html">FilesystemLastModifiedTimeProvider</a></div></li></ul></div></li><li data-name="class:App_Services_LastModified_LastModified" ><div style="padding-left:62px" class="hd leaf"><a href="App/Services/LastModified/LastModified.html">LastModified</a></div></li></ul></div></li><li data-name="namespace:App_Services_Status" ><div style="padding-left:36px" class="hd"><span class="icon icon-play"></span><a href="App/Services/Status.html">Status</a></div><div class="bd"><ul><li data-name="namespace:App_Services_Status_Providers" ><div style="padding-left:54px" class="hd"><span class="icon icon-play"></span><a href="App/Services/Status/Providers.html">Providers</a></div><div class="bd"><ul><li data-name="class:App_Services_Status_Providers_OpcacheProvider" ><div style="padding-left:80px" class="hd leaf"><a href="App/Services/Status/Providers/OpcacheProvider.html">OpcacheProvider</a></div></li><li data-name="class:App_Services_Status_Providers_PdoProvider" ><div style="padding-left:80px" class="hd leaf"><a href="App/Services/Status/Providers/PdoProvider.html">PdoProvider</a></div></li><li data-name="class:App_Services_Status_Providers_PhpRedisProvider" ><div style="padding-left:80px" class="hd leaf"><a href="App/Services/Status/Providers/PhpRedisProvider.html">PhpRedisProvider</a></div></li><li data-name="class:App_Services_Status_Providers_PredisProvider" ><div style="padding-left:80px" class="hd leaf"><a href="App/Services/Status/Providers/PredisProvider.html">PredisProvider</a></div></li><li data-name="class:App_Services_Status_Providers_WebsiteProvider" ><div style="padding-left:80px" class="hd leaf"><a href="App/Services/Status/Providers/WebsiteProvider.html">WebsiteProvider</a></div></li></ul></div></li><li data-name="class:App_Services_Status_StatusService" ><div style="padding-left:62px" class="hd leaf"><a href="App/Services/Status/StatusService.html">StatusService</a></div></li></ul></div></li></ul></div></li></ul></div></li></ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                        {"type":"Namespace","link":"App.html","name":"App","doc":"Namespace App"},{"type":"Namespace","link":"App/Contracts.html","name":"App\\Contracts","doc":"Namespace App\\Contracts"},{"type":"Namespace","link":"App/Contracts/Services.html","name":"App\\Contracts\\Services","doc":"Namespace App\\Contracts\\Services"},{"type":"Namespace","link":"App/Contracts/Services/LastModified.html","name":"App\\Contracts\\Services\\LastModified","doc":"Namespace App\\Contracts\\Services\\LastModified"},{"type":"Namespace","link":"App/Contracts/Services/Status.html","name":"App\\Contracts\\Services\\Status","doc":"Namespace App\\Contracts\\Services\\Status"},{"type":"Namespace","link":"App/Services.html","name":"App\\Services","doc":"Namespace App\\Services"},{"type":"Namespace","link":"App/Services/LastModified.html","name":"App\\Services\\LastModified","doc":"Namespace App\\Services\\LastModified"},{"type":"Namespace","link":"App/Services/LastModified/Providers.html","name":"App\\Services\\LastModified\\Providers","doc":"Namespace App\\Services\\LastModified\\Providers"},{"type":"Namespace","link":"App/Services/Status.html","name":"App\\Services\\Status","doc":"Namespace App\\Services\\Status"},{"type":"Namespace","link":"App/Services/Status/Providers.html","name":"App\\Services\\Status\\Providers","doc":"Namespace App\\Services\\Status\\Providers"},                                                 {"type":"Interface","fromName":"App\\Contracts\\Services\\LastModified","fromLink":"App/Contracts/Services/LastModified.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService","doc":"<p>Interface LastModifiedService.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_addProvider","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::addProvider","doc":"<p>Adds a <a href=\"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html\">\\App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider</a> to the service.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_removeProvider","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::removeProvider","doc":"<p>Removes the named provider from the service.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getProviders","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getProviders","doc":"<p>Get array of providers registered. Returns an array of\n<a href=\"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html\">\\App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider</a>.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getProviderNames","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getProviderNames","doc":"<p>Get array of registered providers names.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getLastModifiedTime","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getLastModifiedTime","doc":"<p>Gets the last modified time from a specific provider or if all is passed in, will\nresolve timestamp from all providers.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getLastModifiedTimeByArray","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getLastModifiedTimeByArray","doc":"<p>Gets the last modified time from an array of providers.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getDefaultTimestampFormat","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getDefaultTimestampFormat","doc":"<p>Get the default timestamp format.</p>"},
            
                                                 {"type":"Interface","fromName":"App\\Contracts\\Services\\LastModified","fromLink":"App/Contracts/Services/LastModified.html","link":"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html","name":"App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider","doc":"<p>Interface LastModifiedTimeProvider.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider","fromLink":"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html","link":"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html#method_getLastModifiedTime","name":"App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider::getLastModifiedTime","doc":"<p>Gets the last modified time for the provider.</p>"},
            
                                                 {"type":"Interface","fromName":"App\\Contracts\\Services\\Status","fromLink":"App/Contracts/Services/Status.html","link":"App/Contracts/Services/Status/StatusService.html","name":"App\\Contracts\\Services\\Status\\StatusService","doc":"<p>Interface StatusService.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_addProvider","name":"App\\Contracts\\Services\\Status\\StatusService::addProvider","doc":"<p>Adds a <a href=\"App/Contracts/Services/Status/StatusServiceProvider.html\">\\App\\Contracts\\Services\\Status\\StatusServiceProvider</a> to the service.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_removeProvider","name":"App\\Contracts\\Services\\Status\\StatusService::removeProvider","doc":"<p>Removes the named provider from the service.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_getProviders","name":"App\\Contracts\\Services\\Status\\StatusService::getProviders","doc":"<p>Get array of providers registered. Returns an array of\n<a href=\"App/Contracts/Services/Status/StatusServiceProvider.html\">\\App\\Contracts\\Services\\Status\\StatusServiceProvider</a>.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_getProviderNames","name":"App\\Contracts\\Services\\Status\\StatusService::getProviderNames","doc":"<p>Get array of registered providers names.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_getStatus","name":"App\\Contracts\\Services\\Status\\StatusService::getStatus","doc":"<p>Get the status for a provider (or all providers if string 'all' or  no param is passed in) or\nlist of providers.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_getStatusByArray","name":"App\\Contracts\\Services\\Status\\StatusService::getStatusByArray","doc":"<p>Get the status for an array of provider names.</p>"},
            
                                                 {"type":"Interface","fromName":"App\\Contracts\\Services\\Status","fromLink":"App/Contracts/Services/Status.html","link":"App/Contracts/Services/Status/StatusServiceProvider.html","name":"App\\Contracts\\Services\\Status\\StatusServiceProvider","doc":"<p>Interface StatusServiceProvider.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusServiceProvider","fromLink":"App/Contracts/Services/Status/StatusServiceProvider.html","link":"App/Contracts/Services/Status/StatusServiceProvider.html#method_getStatus","name":"App\\Contracts\\Services\\Status\\StatusServiceProvider::getStatus","doc":"<p>Get the status of the service.</p>"},
            
                                                        {"type":"Class","fromName":"App\\Contracts\\Services","fromLink":"App/Contracts/Services.html","link":"App/Contracts/Services/CacheException.html","name":"App\\Contracts\\Services\\CacheException","doc":"<p>Class CacheException.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\CacheException","fromLink":"App/Contracts/Services/CacheException.html","link":"App/Contracts/Services/CacheException.html#method_createForTimestampSaveFailure","name":"App\\Contracts\\Services\\CacheException::createForTimestampSaveFailure","doc":"<p>Creates a new <a href=\"App/Contracts/Services/CacheException.html\">\\App\\Contracts\\Services\\CacheException</a> instance for a\ncache save failure when saving a timestamp to cache.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\CacheException","fromLink":"App/Contracts/Services/CacheException.html","link":"App/Contracts/Services/CacheException.html#method_createForStatusSaveFailure","name":"App\\Contracts\\Services\\CacheException::createForStatusSaveFailure","doc":"<p>Creates a new <a href=\"App/Contracts/Services/CacheException.html\">\\App\\Contracts\\Services\\CacheException</a> instance for a\ncache save failure when tryign to save a status array in the cache.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\CacheException","fromLink":"App/Contracts/Services/CacheException.html","link":"App/Contracts/Services/CacheException.html#method_createFromException","name":"App\\Contracts\\Services\\CacheException::createFromException","doc":"<p>Creates a new <a href=\"App/Contracts/Services/CacheException.html\">\\App\\Contracts\\Services\\CacheException</a> from an\nexisting <a href=\"https://www.php.net/Throwable\">Throwable</a>.</p>"},
            
                                                {"type":"Class","fromName":"App\\Contracts\\Services","fromLink":"App/Contracts/Services.html","link":"App/Contracts/Services/CacheImplementationNeededException.html","name":"App\\Contracts\\Services\\CacheImplementationNeededException","doc":"<p>Class CacheImplementationNeededException.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\CacheImplementationNeededException","fromLink":"App/Contracts/Services/CacheImplementationNeededException.html","link":"App/Contracts/Services/CacheImplementationNeededException.html#method_cacheImplementationNeeded","name":"App\\Contracts\\Services\\CacheImplementationNeededException::cacheImplementationNeeded","doc":"<p>Creates a new <a href=\"App/Contracts/Services/CacheImplementationNeededException.html\">\\App\\Contracts\\Services\\CacheImplementationNeededException</a> instance.</p>"},
            
                                                {"type":"Class","fromName":"App\\Contracts\\Services","fromLink":"App/Contracts/Services.html","link":"App/Contracts/Services/InvalidDateFormatException.html","name":"App\\Contracts\\Services\\InvalidDateFormatException","doc":"<p>Class InvalidDateFormatException.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\InvalidDateFormatException","fromLink":"App/Contracts/Services/InvalidDateFormatException.html","link":"App/Contracts/Services/InvalidDateFormatException.html#method_invalidFormat","name":"App\\Contracts\\Services\\InvalidDateFormatException::invalidFormat","doc":"<p>Creates a new exception when an invlaid date format is encountered.</p>"},
            
                                                {"type":"Class","fromName":"App\\Contracts\\Services\\LastModified","fromLink":"App/Contracts/Services/LastModified.html","link":"App/Contracts/Services/LastModified/LastModifiedOptions.html","name":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions","doc":"<p>Class LastModifiedOptions.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions","fromLink":"App/Contracts/Services/LastModified/LastModifiedOptions.html","link":"App/Contracts/Services/LastModified/LastModifiedOptions.html#method___construct","name":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions::__construct","doc":"<p>Constructs a new set of <a href=\"App/Contracts/Services/LastModified/LastModifiedService.html\">\\App\\Contracts\\Services\\LastModified\\LastModifiedService</a> options.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions","fromLink":"App/Contracts/Services/LastModified/LastModifiedOptions.html","link":"App/Contracts/Services/LastModified/LastModifiedOptions.html#method_isCacheEnabled","name":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions::isCacheEnabled","doc":"<p>Get whether caching is enabled.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions","fromLink":"App/Contracts/Services/LastModified/LastModifiedOptions.html","link":"App/Contracts/Services/LastModified/LastModifiedOptions.html#method_getCacheTtl","name":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions::getCacheTtl","doc":"<p>Get cache TTL option.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions","fromLink":"App/Contracts/Services/LastModified/LastModifiedOptions.html","link":"App/Contracts/Services/LastModified/LastModifiedOptions.html#method_getCacheKey","name":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions::getCacheKey","doc":"<p>Get cache key option.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions","fromLink":"App/Contracts/Services/LastModified/LastModifiedOptions.html","link":"App/Contracts/Services/LastModified/LastModifiedOptions.html#method_getTimestampFormat","name":"App\\Contracts\\Services\\LastModified\\LastModifiedOptions::getTimestampFormat","doc":"<p>Get timestamp format option.</p>"},
            
                                                {"type":"Class","fromName":"App\\Contracts\\Services\\LastModified","fromLink":"App/Contracts/Services/LastModified.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService","doc":"<p>Interface LastModifiedService.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_addProvider","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::addProvider","doc":"<p>Adds a <a href=\"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html\">\\App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider</a> to the service.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_removeProvider","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::removeProvider","doc":"<p>Removes the named provider from the service.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getProviders","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getProviders","doc":"<p>Get array of providers registered. Returns an array of\n<a href=\"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html\">\\App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider</a>.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getProviderNames","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getProviderNames","doc":"<p>Get array of registered providers names.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getLastModifiedTime","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getLastModifiedTime","doc":"<p>Gets the last modified time from a specific provider or if all is passed in, will\nresolve timestamp from all providers.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getLastModifiedTimeByArray","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getLastModifiedTimeByArray","doc":"<p>Gets the last modified time from an array of providers.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedService","fromLink":"App/Contracts/Services/LastModified/LastModifiedService.html","link":"App/Contracts/Services/LastModified/LastModifiedService.html#method_getDefaultTimestampFormat","name":"App\\Contracts\\Services\\LastModified\\LastModifiedService::getDefaultTimestampFormat","doc":"<p>Get the default timestamp format.</p>"},
            
                                                {"type":"Class","fromName":"App\\Contracts\\Services\\LastModified","fromLink":"App/Contracts/Services/LastModified.html","link":"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html","name":"App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider","doc":"<p>Interface LastModifiedTimeProvider.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider","fromLink":"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html","link":"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html#method_getLastModifiedTime","name":"App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider::getLastModifiedTime","doc":"<p>Gets the last modified time for the provider.</p>"},
            
                                                {"type":"Class","fromName":"App\\Contracts\\Services","fromLink":"App/Contracts/Services.html","link":"App/Contracts/Services/ProviderRegistrationException.html","name":"App\\Contracts\\Services\\ProviderRegistrationException","doc":"<p>Class ProviderRegistrationException.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\ProviderRegistrationException","fromLink":"App/Contracts/Services/ProviderRegistrationException.html","link":"App/Contracts/Services/ProviderRegistrationException.html#method_providerAlreadyRegistered","name":"App\\Contracts\\Services\\ProviderRegistrationException::providerAlreadyRegistered","doc":"<p>Creates a new <a href=\"App/Contracts/Services/ProviderRegistrationException.html\">\\App\\Contracts\\Services\\ProviderRegistrationException</a> when a provider\nwith name $providerName is already registered.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\ProviderRegistrationException","fromLink":"App/Contracts/Services/ProviderRegistrationException.html","link":"App/Contracts/Services/ProviderRegistrationException.html#method_noProviderRegistered","name":"App\\Contracts\\Services\\ProviderRegistrationException::noProviderRegistered","doc":"<p>Creates a new <a href=\"App/Contracts/Services/ProviderRegistrationException.html\">\\App\\Contracts\\Services\\ProviderRegistrationException</a> for when\nno provider with $providerName is registered.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\ProviderRegistrationException","fromLink":"App/Contracts/Services/ProviderRegistrationException.html","link":"App/Contracts/Services/ProviderRegistrationException.html#method_noProvidersSpecified","name":"App\\Contracts\\Services\\ProviderRegistrationException::noProvidersSpecified","doc":"<p>Creates a new <a href=\"App/Contracts/Services/ProviderRegistrationException.html\">\\App\\Contracts\\Services\\ProviderRegistrationException</a> for\nwhen no providers where specified.</p>"},
            
                                                {"type":"Class","fromName":"App\\Contracts\\Services\\Status","fromLink":"App/Contracts/Services/Status.html","link":"App/Contracts/Services/Status/StatusOptions.html","name":"App\\Contracts\\Services\\Status\\StatusOptions","doc":"<p>Class StatusOptions.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusOptions","fromLink":"App/Contracts/Services/Status/StatusOptions.html","link":"App/Contracts/Services/Status/StatusOptions.html#method___construct","name":"App\\Contracts\\Services\\Status\\StatusOptions::__construct","doc":"<p>Constructs a new set of <a href=\"App/Contracts/Services/Status/StatusService.html\">\\App\\Contracts\\Services\\Status\\StatusService</a> options.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusOptions","fromLink":"App/Contracts/Services/Status/StatusOptions.html","link":"App/Contracts/Services/Status/StatusOptions.html#method_isCacheEnabled","name":"App\\Contracts\\Services\\Status\\StatusOptions::isCacheEnabled","doc":"<p>Get whether caching is enabled.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusOptions","fromLink":"App/Contracts/Services/Status/StatusOptions.html","link":"App/Contracts/Services/Status/StatusOptions.html#method_getCacheTtl","name":"App\\Contracts\\Services\\Status\\StatusOptions::getCacheTtl","doc":"<p>Get cache TTL option.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusOptions","fromLink":"App/Contracts/Services/Status/StatusOptions.html","link":"App/Contracts/Services/Status/StatusOptions.html#method_getCacheKey","name":"App\\Contracts\\Services\\Status\\StatusOptions::getCacheKey","doc":"<p>Get cache key option.</p>"},
            
                                                {"type":"Class","fromName":"App\\Contracts\\Services\\Status","fromLink":"App/Contracts/Services/Status.html","link":"App/Contracts/Services/Status/StatusService.html","name":"App\\Contracts\\Services\\Status\\StatusService","doc":"<p>Interface StatusService.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_addProvider","name":"App\\Contracts\\Services\\Status\\StatusService::addProvider","doc":"<p>Adds a <a href=\"App/Contracts/Services/Status/StatusServiceProvider.html\">\\App\\Contracts\\Services\\Status\\StatusServiceProvider</a> to the service.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_removeProvider","name":"App\\Contracts\\Services\\Status\\StatusService::removeProvider","doc":"<p>Removes the named provider from the service.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_getProviders","name":"App\\Contracts\\Services\\Status\\StatusService::getProviders","doc":"<p>Get array of providers registered. Returns an array of\n<a href=\"App/Contracts/Services/Status/StatusServiceProvider.html\">\\App\\Contracts\\Services\\Status\\StatusServiceProvider</a>.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_getProviderNames","name":"App\\Contracts\\Services\\Status\\StatusService::getProviderNames","doc":"<p>Get array of registered providers names.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_getStatus","name":"App\\Contracts\\Services\\Status\\StatusService::getStatus","doc":"<p>Get the status for a provider (or all providers if string 'all' or  no param is passed in) or\nlist of providers.</p>"},
        {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusService","fromLink":"App/Contracts/Services/Status/StatusService.html","link":"App/Contracts/Services/Status/StatusService.html#method_getStatusByArray","name":"App\\Contracts\\Services\\Status\\StatusService::getStatusByArray","doc":"<p>Get the status for an array of provider names.</p>"},
            
                                                {"type":"Class","fromName":"App\\Contracts\\Services\\Status","fromLink":"App/Contracts/Services/Status.html","link":"App/Contracts/Services/Status/StatusServiceProvider.html","name":"App\\Contracts\\Services\\Status\\StatusServiceProvider","doc":"<p>Interface StatusServiceProvider.</p>"},
                                {"type":"Method","fromName":"App\\Contracts\\Services\\Status\\StatusServiceProvider","fromLink":"App/Contracts/Services/Status/StatusServiceProvider.html","link":"App/Contracts/Services/Status/StatusServiceProvider.html#method_getStatus","name":"App\\Contracts\\Services\\Status\\StatusServiceProvider::getStatus","doc":"<p>Get the status of the service.</p>"},
            
                                                {"type":"Class","fromName":"App\\Services\\LastModified","fromLink":"App/Services/LastModified.html","link":"App/Services/LastModified/LastModified.html","name":"App\\Services\\LastModified\\LastModified","doc":"<p>Class LastModified.</p>"},
                                {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method___construct","name":"App\\Services\\LastModified\\LastModified::__construct","doc":"<p>Constructs a LastModified service object.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_addProvider","name":"App\\Services\\LastModified\\LastModified::addProvider","doc":"<p>Adds a <a href=\"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html\">\\App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider</a> to the service.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_removeProvider","name":"App\\Services\\LastModified\\LastModified::removeProvider","doc":"<p>Removes the named provider from the service.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_getProviders","name":"App\\Services\\LastModified\\LastModified::getProviders","doc":"<p>Get array of providers registered. Returns an array of\n<a href=\"App/Contracts/Services/LastModified/LastModifiedTimeProvider.html\">\\App\\Contracts\\Services\\LastModified\\LastModifiedTimeProvider</a>.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_getProviderNames","name":"App\\Services\\LastModified\\LastModified::getProviderNames","doc":"<p>Get array of registered providers names.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_getLastModifiedTime","name":"App\\Services\\LastModified\\LastModified::getLastModifiedTime","doc":"<p>Gets the last modified time from a specific provider or if all is passed in, will\nresolve timestamp from all providers.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_getLastModifiedTimeByArray","name":"App\\Services\\LastModified\\LastModified::getLastModifiedTimeByArray","doc":"<p>Gets the last modified time from an array of providers.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_getDefaultTimestampFormat","name":"App\\Services\\LastModified\\LastModified::getDefaultTimestampFormat","doc":"<p>Get the default timestamp format.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_resolveProviderArray","name":"App\\Services\\LastModified\\LastModified::resolveProviderArray","doc":"<p>Get last modified timestamp for an array of providers.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_resolveProviderTimestamps","name":"App\\Services\\LastModified\\LastModified::resolveProviderTimestamps","doc":"<p>Resolve latest timestamp from an array of provider names.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_resolveTimestamp","name":"App\\Services\\LastModified\\LastModified::resolveTimestamp","doc":"<p>Resolve timestamp for a specific provider.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_checkCache","name":"App\\Services\\LastModified\\LastModified::checkCache","doc":"<p>Check the cache for the given key and return it iff it exists, otherwise return null.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\LastModified","fromLink":"App/Services/LastModified/LastModified.html","link":"App/Services/LastModified/LastModified.html#method_saveInCache","name":"App\\Services\\LastModified\\LastModified::saveInCache","doc":"<p>Saves timestamp in cache.</p>"},
            
                                                {"type":"Class","fromName":"App\\Services\\LastModified\\Providers","fromLink":"App/Services/LastModified/Providers.html","link":"App/Services/LastModified/Providers/CacheLastModifiedTimeProvider.html","name":"App\\Services\\LastModified\\Providers\\CacheLastModifiedTimeProvider","doc":"<p>Class CacheLastModifiedTimeProvider.</p>"},
                                {"type":"Method","fromName":"App\\Services\\LastModified\\Providers\\CacheLastModifiedTimeProvider","fromLink":"App/Services/LastModified/Providers/CacheLastModifiedTimeProvider.html","link":"App/Services/LastModified/Providers/CacheLastModifiedTimeProvider.html#method___construct","name":"App\\Services\\LastModified\\Providers\\CacheLastModifiedTimeProvider::__construct","doc":"<p>Constructs a cache based last modified provider.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\Providers\\CacheLastModifiedTimeProvider","fromLink":"App/Services/LastModified/Providers/CacheLastModifiedTimeProvider.html","link":"App/Services/LastModified/Providers/CacheLastModifiedTimeProvider.html#method_getLastModifiedTime","name":"App\\Services\\LastModified\\Providers\\CacheLastModifiedTimeProvider::getLastModifiedTime","doc":"<p>Gets the last modified time for the provider.</p>"},
            
                                                {"type":"Class","fromName":"App\\Services\\LastModified\\Providers","fromLink":"App/Services/LastModified/Providers.html","link":"App/Services/LastModified/Providers/FilesystemLastModifiedTimeProvider.html","name":"App\\Services\\LastModified\\Providers\\FilesystemLastModifiedTimeProvider","doc":"<p>Class FilesystemLastModifiedTimeProvider.</p>"},
                                {"type":"Method","fromName":"App\\Services\\LastModified\\Providers\\FilesystemLastModifiedTimeProvider","fromLink":"App/Services/LastModified/Providers/FilesystemLastModifiedTimeProvider.html","link":"App/Services/LastModified/Providers/FilesystemLastModifiedTimeProvider.html#method___construct","name":"App\\Services\\LastModified\\Providers\\FilesystemLastModifiedTimeProvider::__construct","doc":"<p>Constructs filesystem last modified provider.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\Providers\\FilesystemLastModifiedTimeProvider","fromLink":"App/Services/LastModified/Providers/FilesystemLastModifiedTimeProvider.html","link":"App/Services/LastModified/Providers/FilesystemLastModifiedTimeProvider.html#method_getLastModifiedTime","name":"App\\Services\\LastModified\\Providers\\FilesystemLastModifiedTimeProvider::getLastModifiedTime","doc":"<p>Gets the last modified time for the provider.</p>"},
        {"type":"Method","fromName":"App\\Services\\LastModified\\Providers\\FilesystemLastModifiedTimeProvider","fromLink":"App/Services/LastModified/Providers/FilesystemLastModifiedTimeProvider.html","link":"App/Services/LastModified/Providers/FilesystemLastModifiedTimeProvider.html#method_findLastModifiedFileTime","name":"App\\Services\\LastModified\\Providers\\FilesystemLastModifiedTimeProvider::findLastModifiedFileTime","doc":"<p>Function to iterate over an array of files/directories and return\nthe greatest file modified time.</p>"},
            
                                                {"type":"Class","fromName":"App\\Services\\Status\\Providers","fromLink":"App/Services/Status/Providers.html","link":"App/Services/Status/Providers/OpcacheProvider.html","name":"App\\Services\\Status\\Providers\\OpcacheProvider","doc":"<p>Class OpcacheProvider.</p>"},
                                {"type":"Method","fromName":"App\\Services\\Status\\Providers\\OpcacheProvider","fromLink":"App/Services/Status/Providers/OpcacheProvider.html","link":"App/Services/Status/Providers/OpcacheProvider.html#method_getStatus","name":"App\\Services\\Status\\Providers\\OpcacheProvider::getStatus","doc":"<p>Get the status of the service.</p>"},
            
                                                {"type":"Class","fromName":"App\\Services\\Status\\Providers","fromLink":"App/Services/Status/Providers.html","link":"App/Services/Status/Providers/PdoProvider.html","name":"App\\Services\\Status\\Providers\\PdoProvider","doc":"<p>Class PdoProvider.</p>"},
                                {"type":"Method","fromName":"App\\Services\\Status\\Providers\\PdoProvider","fromLink":"App/Services/Status/Providers/PdoProvider.html","link":"App/Services/Status/Providers/PdoProvider.html#method___construct","name":"App\\Services\\Status\\Providers\\PdoProvider::__construct","doc":"<p>Constructs a new database status service provider.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\Providers\\PdoProvider","fromLink":"App/Services/Status/Providers/PdoProvider.html","link":"App/Services/Status/Providers/PdoProvider.html#method_getStatus","name":"App\\Services\\Status\\Providers\\PdoProvider::getStatus","doc":"<p>Get the status of the service.</p>"},
            
                                                {"type":"Class","fromName":"App\\Services\\Status\\Providers","fromLink":"App/Services/Status/Providers.html","link":"App/Services/Status/Providers/PhpRedisProvider.html","name":"App\\Services\\Status\\Providers\\PhpRedisProvider","doc":"<p>Class PhpRedisProvider.</p>"},
                                {"type":"Method","fromName":"App\\Services\\Status\\Providers\\PhpRedisProvider","fromLink":"App/Services/Status/Providers/PhpRedisProvider.html","link":"App/Services/Status/Providers/PhpRedisProvider.html#method___construct","name":"App\\Services\\Status\\Providers\\PhpRedisProvider::__construct","doc":"<p>Construct a new PHP redis status provider.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\Providers\\PhpRedisProvider","fromLink":"App/Services/Status/Providers/PhpRedisProvider.html","link":"App/Services/Status/Providers/PhpRedisProvider.html#method_getStatus","name":"App\\Services\\Status\\Providers\\PhpRedisProvider::getStatus","doc":"<p>Get the status of the service.</p>"},
            
                                                {"type":"Class","fromName":"App\\Services\\Status\\Providers","fromLink":"App/Services/Status/Providers.html","link":"App/Services/Status/Providers/PredisProvider.html","name":"App\\Services\\Status\\Providers\\PredisProvider","doc":"<p>Class PredisProvider.</p>"},
                                {"type":"Method","fromName":"App\\Services\\Status\\Providers\\PredisProvider","fromLink":"App/Services/Status/Providers/PredisProvider.html","link":"App/Services/Status/Providers/PredisProvider.html#method___construct","name":"App\\Services\\Status\\Providers\\PredisProvider::__construct","doc":"<p>Construct a new predis status provider.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\Providers\\PredisProvider","fromLink":"App/Services/Status/Providers/PredisProvider.html","link":"App/Services/Status/Providers/PredisProvider.html#method_getStatus","name":"App\\Services\\Status\\Providers\\PredisProvider::getStatus","doc":"<p>Get the status of the service.</p>"},
            
                                                {"type":"Class","fromName":"App\\Services\\Status\\Providers","fromLink":"App/Services/Status/Providers.html","link":"App/Services/Status/Providers/WebsiteProvider.html","name":"App\\Services\\Status\\Providers\\WebsiteProvider","doc":"<p>Class WebsiteProvider.</p>"},
                                {"type":"Method","fromName":"App\\Services\\Status\\Providers\\WebsiteProvider","fromLink":"App/Services/Status/Providers/WebsiteProvider.html","link":"App/Services/Status/Providers/WebsiteProvider.html#method___construct","name":"App\\Services\\Status\\Providers\\WebsiteProvider::__construct","doc":"<p>Construct a new website status provider.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\Providers\\WebsiteProvider","fromLink":"App/Services/Status/Providers/WebsiteProvider.html","link":"App/Services/Status/Providers/WebsiteProvider.html#method_getStatus","name":"App\\Services\\Status\\Providers\\WebsiteProvider::getStatus","doc":"<p>Get the status of the service.</p>"},
            
                                                {"type":"Class","fromName":"App\\Services\\Status","fromLink":"App/Services/Status.html","link":"App/Services/Status/StatusService.html","name":"App\\Services\\Status\\StatusService","doc":"<p>Class StatusService.</p>"},
                                {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method___construct","name":"App\\Services\\Status\\StatusService::__construct","doc":"<p>Construct a new status service.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_addProvider","name":"App\\Services\\Status\\StatusService::addProvider","doc":"<p>Adds a <a href=\"App/Contracts/Services/Status/StatusServiceProvider.html\">\\App\\Contracts\\Services\\Status\\StatusServiceProvider</a> to the service.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_removeProvider","name":"App\\Services\\Status\\StatusService::removeProvider","doc":"<p>Removes the named provider from the service.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_getProviders","name":"App\\Services\\Status\\StatusService::getProviders","doc":"<p>Get array of providers registered. Returns an array of\n<a href=\"App/Contracts/Services/Status/StatusServiceProvider.html\">\\App\\Contracts\\Services\\Status\\StatusServiceProvider</a>.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_getProviderNames","name":"App\\Services\\Status\\StatusService::getProviderNames","doc":"<p>Get array of registered providers names.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_getStatus","name":"App\\Services\\Status\\StatusService::getStatus","doc":"<p>Get the status for a provider (or all providers if string 'all' or  no param is passed in) or\nlist of providers.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_getStatusByArray","name":"App\\Services\\Status\\StatusService::getStatusByArray","doc":"<p>Get the status for an array of provider names.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_resolveProviderArray","name":"App\\Services\\Status\\StatusService::resolveProviderArray","doc":"<p>Resolve statuses of an array of provider names.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_resolveStatus","name":"App\\Services\\Status\\StatusService::resolveStatus","doc":"<p>Resolves a status for a specific provider.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_checkCache","name":"App\\Services\\Status\\StatusService::checkCache","doc":"<p>Check the cache for the given key and return it if it exists, otherwise return null.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_resolveCachedStatus","name":"App\\Services\\Status\\StatusService::resolveCachedStatus","doc":"<p>Resolve cached status from cache. If no cache entry is found or cannot be resolve, null will\nbe returned.</p>"},
        {"type":"Method","fromName":"App\\Services\\Status\\StatusService","fromLink":"App/Services/Status/StatusService.html","link":"App/Services/Status/StatusService.html#method_saveInCache","name":"App\\Services\\Status\\StatusService::saveInCache","doc":"<p>Saves status in cache.</p>"},
            
                                // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Doctum = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Doctum.injectApiTree($('#api-tree'));
    });

    return root.Doctum;
})(window);

$(function() {

    
    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').on('click', function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Doctum.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


