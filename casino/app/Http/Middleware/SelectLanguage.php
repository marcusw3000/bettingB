<?php 
namespace VanguardLTE\Http\Middleware
{
    class SelectLanguage
    {
        public function handle($request, \Closure $next)
        {
            // PROMEX is intentionally single-language. Do not honour the old
            // per-user value or browser cookie, otherwise English can return
            // after a login or a previously stored preference.
            \App::setLocale('pt-br');

            return $next($request);
        }
    }

}
