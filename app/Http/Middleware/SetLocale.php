<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Mcamara\LaravelLocalization\LaravelLocalization;

class SetLocale
{
    /**
     * @var \Mcamara\LaravelLocalization\LaravelLocalization
     */
    protected $localization;

    public function __construct(LaravelLocalization $localization)
    {
        $this->localization = $localization;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     *
     * @throws \Mcamara\LaravelLocalization\Exceptions\SupportedLocalesNotDefined
     */
    public function handle($request, Closure $next)
    {
        $currentLocale = $this->localization->getCurrentLocale();
        $supportedLocales = $this->localization->getSupportedLocales();

        $localeRegional = $supportedLocales[$currentLocale]['regional'];
        $localeWin = $supportedLocales[$currentLocale]['locale_win'];

        /*
         * setLocale for php. Enables localized dates, format numbers, etc.
         */
        setlocale(LC_ALL,
            $localeRegional,
            "${localeRegional}.utf-8",
            "${localeRegional}.iso-8859-1",
            $localeWin
        );

        /*
         * setLocale to use Carbon source locales. Enables diffForHumans() localized
         */
        Carbon::setLocale($currentLocale);
        Carbon::setUtf8(true);

        /*
         * Set Captcha locale
         */
        app('config')->set('no-captcha.lang', $currentLocale);

        return $next($request);
    }
}
