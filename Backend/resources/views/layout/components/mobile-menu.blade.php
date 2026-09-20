<!-- BEGIN: Mobile Menu -->
<div class="mobile-menu md:hidden">
    <div class="mobile-menu-bar" style="background-color:#426f7f">
        <a href="" class="flex mr-auto">
            @php
                $logo = DB::table('systemflag')
                    ->where('name', 'AdminLogo')
                    ->select('value')
                    ->first();
            @endphp
            <img alt="Online Puja Admin" class="w-6" src="/{{ $logo->value ?? 'images/logo.png' }}">
        </a>
        <a href="javascript:;" class="mobile-menu-toggler">
            <i data-lucide="bar-chart-2" class="w-8 h-8 text-white transform -rotate-90"></i>
        </a>
    </div>
    <div class="scrollable">
        <a href="javascript:;" class="mobile-menu-toggler">
            <i data-lucide="x-circle" class="w-8 h-8 text-white transform -rotate-90"></i>
        </a>
        <ul class="scrollable__content py-2">
            @php
                $appName = strtolower(getProfessionTitle() ?: 'partner');
                $side_menu = \App\Main\SideMenu::dynamicMenu();
                $firstLevelActive = $first_level_active_index ?? '';
                $secondLevelActive = $second_level_active_index ?? '';
                $thirdLevelActive = $third_level_active_index ?? '';
            @endphp
            @foreach ($side_menu as $menuKey => $menu)
                @if ($menu == 'devider')
                    <li class="menu__devider my-6"></li>
                @else
                    @php
                        $hasActiveChild = false;
                        if (isset($menu->sub_menu) && is_iterable($menu->sub_menu)) {
                            foreach ($menu->sub_menu as $sm) {
                                if (!empty($sm->route) && request()->routeIs($sm->route)) {
                                    $hasActiveChild = true;
                                    break;
                                }
                            }
                        }
                        $isFirstActive = ($firstLevelActive == $menuKey) || (!empty($menu->route) && request()->routeIs($menu->route)) || $hasActiveChild;
                    @endphp
                    <li>
                        <a href="{{ (!empty($menu->route) && \Illuminate\Support\Facades\Route::has($menu->route)) ? route($menu->route) : 'javascript:;' }}"
                            class="{{ $isFirstActive ? 'menu menu--active' : 'menu' }}">
                            <div class="menu__icon">
                                <i data-lucide="{{ $menu->icon ?: 'circle' }}"></i>
                            </div>
                            <div class="menu__title">
                                @if($menu->pageName=='Astrologers')
                                {{ $appName }}
                                @else
                                {{ $menu->pageName }}
                                @endif
                                @if (isset($menu->sub_menu) && count($menu->sub_menu) > 0)
                                    <i data-lucide="chevron-down"
                                        class="menu__sub-icon {{ $isFirstActive ? 'transform rotate-180' : '' }}"></i>
                                @endif
                            </div>
                        </a>
                        @if (isset($menu->sub_menu) && count($menu->sub_menu) > 0)
                            <ul class="{{ $isFirstActive ? 'menu__sub-open' : '' }}">
                                @foreach ($menu->sub_menu as $subMenuKey => $subMenu)
                                    @php
                                        $isSecondActive = ($secondLevelActive == $subMenuKey) || (!empty($subMenu->route) && request()->routeIs($subMenu->route));
                                    @endphp
                                    <li>
                                        <a href="{{ (!empty($subMenu->route) && \Illuminate\Support\Facades\Route::has($subMenu->route)) ? route($subMenu->route) : 'javascript:;' }}"
                                            class="{{ $isSecondActive ? 'menu menu--active' : 'menu' }}">
                                            <div class="menu__icon">
                                                <i data-lucide="{{ $subMenu->icon ?: 'circle' }}"></i>
                                            </div>
                                            <div class="menu__title">
                                                @if(preg_match('/Astrologer(s)?/i', $subMenu->pageName))
                                                {{ preg_replace('/Astrologer(s)?/i',$appName, $subMenu->pageName) }}
                                                @else
                                                    {{ $subMenu->pageName }}
                                                @endif
                                                @if (isset($subMenu->sub_menu) && count($subMenu->sub_menu) > 0)
                                                    <i data-lucide="chevron-down"
                                                        class="menu__sub-icon {{ $isSecondActive ? 'transform rotate-180' : '' }}"></i>
                                                @endif
                                            </div>
                                        </a>
                                        @if (isset($subMenu->sub_menu) && count($subMenu->sub_menu) > 0)
                                            <ul class="{{ $isSecondActive ? 'menu__sub-open' : '' }}">
                                                @foreach ($subMenu->sub_menu as $lastSubMenuKey => $lastSubMenu)
                                                    @php
                                                        $isThirdActive = ($thirdLevelActive == $lastSubMenuKey) || (!empty($lastSubMenu->route) && request()->routeIs($lastSubMenu->route));
                                                    @endphp
                                                    <li>
                                                        <a href="{{ (!empty($lastSubMenu->route) && \Illuminate\Support\Facades\Route::has($lastSubMenu->route)) ? route($lastSubMenu->route) : 'javascript:;' }}"
                                                            class="{{ $isThirdActive ? 'menu menu--active' : 'menu' }}">
                                                            <div class="menu__icon">
                                                                <i data-lucide="{{ $lastSubMenu->icon ?: 'zap' }}"></i>
                                                            </div>
                                                            <div class="menu__title">{{ $lastSubMenu->pageName }}</div>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
<!-- END: Mobile Menu -->
