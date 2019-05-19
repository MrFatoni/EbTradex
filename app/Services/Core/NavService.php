<?php
/**
 * Created by PhpStorm.
 * User: zahid
 * Date: 31/5/18
 * Time: 4:15 PM
 */

namespace App\Services\Core;


use App\Repositories\Core\Interfaces\NavigationInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavService
{
    public $navigation;

    public function __construct(NavigationInterface $navigation)
    {
        $this->navigation = $navigation;
    }

    //--------------- frontend navigation functions ------------------------

    public function navigationSingle($navPlace, $template = 'default_nav')
    {
        $navData = $this->navigation->getBySlug($navPlace);
        if ($navData) {
            $navData = $navData->navigation_items;
        } else {
            return '';
        }
        if (!isset($navConfig['navigation_template'][$template])) {
            $template = 'default_nav';
        }
        $navConfig = config('navigation');
        $navTemplate = $this->_template_builder($navConfig['navigation_template'][$template]);
        return $this->_navigationBuilder($navData, $navTemplate);
    }

    protected function _template_builder($navTemplate)
    {
        $all_features = [
            'navigation_item_beginning_wrapper_start' => '',
            'navigation_item_beginning_wrapper_end' => '',
            'navigation_item_text_wrapper_start' => '',
            'navigation_item_text_wrapper_end' => '',
            'navigation_item_ending_wrapper_start' => '',
            'navigation_item_ending_wrapper_end' => '',
            'navigation_item_icon_wrapper_start' => '',
            'navigation_item_icon_wrapper_end' => '',

            'navigation_sub_menu_wrapper_start' => '',
            'navigation_sub_menu_wrapper_end' => '',
            'navigation_item_wrapper_in_sub_menu_start' => '',
            'navigation_item_wrapper_in_sub_menu_end' => '',

            'navigation_item_icon_position' => '',
            'navigation_item_link_class' => '',
            'navigation_item_link_active_class' => '',
            'navigation_item_active_class_on_anchor_tag' => false,
            'navigation_item_no_link_text' => 'javascript:;',

            'mega_menu_wrapper_start' => '',
            'mega_menu_wrapper_end' => '',
            'mega_menu_section_wrapper_start' => '',
            'mega_menu_section_wrapper_end' => '',
        ];
        return array_merge($navTemplate, array_diff_key($all_features, $navTemplate));
    }

    protected function _navigationBuilder($navData, $navTemplate)
    {
        $allRoutes = \Route::getRoutes()->getRoutesByMethod()['GET'];
        $allPermission = config('permissionRoutes.all_accessible_routes');
        $allAvailableRoutes = [];
        foreach ($allRoutes as $routeName => $routeData) {
            $middleware = $routeData->middleware();
            $parameters = $routeData->signatureParameters();
            $isMenuable = true;
            foreach ($parameters as $parameter) {
                if (!$parameter->isOptional())
                    $isMenuable = false;
                break;
            }
            if ($isMenuable && Auth::user() && in_array('permission', $middleware) && has_permission($routeData->getName())) {
                $allAvailableRoutes[] = $routeData->getName();
            } elseif ($isMenuable && !Auth::user() && in_array('guest.permission', $middleware)) {
                $allAvailableRoutes[] = $routeData->getName();
            } elseif ($isMenuable && in_array('verification.permission', $middleware) &&
                (
                    (!Auth::user() || (Auth::user() && Auth::user()->is_email_verified == EMAIL_VERIFICATION_STATUS_INACTIVE) )  &&
                    admin_settings('require_email_verification') == ACTIVE_STATUS_ACTIVE
                )
            ) {
                $allAvailableRoutes[] = $routeData->getName();
            } elseif ($isMenuable && !empty($middleware) && !in_array('permission', $middleware) && !in_array('guest.permission', $middleware) &&
                !in_array('verification.permission', $middleware) && in_array('web', $middleware) && !in_array('Barryvdh\Debugbar\Middleware\DebugbarEnabled', $middleware)) {
                $allAvailableRoutes[] = $routeData->getName();
            } else {
                continue;
            }
        }

        $arrayColumn = array_column($navData, 'parent_id');
        $output = $this->_tagBuilder($navTemplate['navigation_wrapper_start']);
        $output .= $this->_navigationInside($navData, $allAvailableRoutes, $navTemplate, $arrayColumn);
        $output .= $navTemplate['navigation_wrapper_end'];
        return $output;
    }

    protected function _tagBuilder($startingWrapper, $dbClass = null, $activeClass = null)
    {
        if ($startingWrapper == null) {
            return '';
        }
        if ($activeClass != null) {
            $dbClass = $dbClass == null ? $activeClass : ($activeClass == null ? '' : $activeClass . ' ' . $dbClass);
        }
        if ($dbClass != null) {
            if (strripos($startingWrapper, 'class="')) {
                $startingWrapper = substr_replace($startingWrapper, 'class="' . $dbClass . ' ', strripos($startingWrapper, 'class="'), 7);
            } elseif (strripos($startingWrapper, "class='")) {
                $startingWrapper = substr_replace($startingWrapper, "class='" . $dbClass . ' ', strripos($startingWrapper, 'class="'), 7);
            } else {
                $startingWrapper = substr_replace($startingWrapper, ' class="' . $dbClass . '">', -1);
            }
        }
        return $startingWrapper;
    }

    protected function _navigationInside($dbData, $allAvailableRoutes, $navTemplate, $arrayColumn, $parentId = 0, $level = 1, $megaMenu = 0)
    {
        $result = '';
        if ($level == 2 && $megaMenu == 1 && $navTemplate['mega_menu_wrapper_start'] != null) {
            $result .= $this->_tagBuilder($navTemplate['mega_menu_wrapper_start']);
        } elseif ($level > 1) {
            if ($navTemplate['navigation_sub_menu_wrapper_start'] != null) {
                $result .= $this->_tagBuilder($navTemplate['navigation_sub_menu_wrapper_start']);
            } else {
                $result .= $this->_tagBuilder($navTemplate['navigation_wrapper_start']);
            }
        }

        foreach ($dbData as $rowKey => $rowValue) {
            if ($rowValue['route'] != '' && !in_array($rowValue['route'], $allAvailableRoutes)) {
                continue;
            }
            if ($rowValue['parent_id'] == $parentId) {
                unset($dbData[$rowKey]);
                $result .= $this->_listItemStartBuilder($rowValue, $navTemplate, $level, $megaMenu);

                if (in_array($rowValue['order'], $arrayColumn)) {
                    $active_mega_menu = $rowValue['mega_menu'] == 1 ? 1 : $megaMenu;
                    $result .= $this->_navigationInside($dbData, $allAvailableRoutes, $navTemplate, $arrayColumn, $rowValue['order'], ($level + 1), $active_mega_menu);
                }

                if ($level == 2 && $megaMenu == 1 && $navTemplate['mega_menu_section_wrapper_start'] != null) {
                    $result .= $this->_tagBuilder($navTemplate['mega_menu_section_wrapper_end']);
                } else {
                    if ($level > 1 && $navTemplate['navigation_item_wrapper_in_sub_menu_start'] != null) {
                        $result .= $this->_tagBuilder($navTemplate['navigation_item_wrapper_in_sub_menu_end']);
                    } else {
                        $result .= $this->_tagBuilder($navTemplate['navigation_item_wrapper_end']);
                    }
                }
            }
        }

        if ($level == 2 && $megaMenu == 1 && $navTemplate['mega_menu_wrapper_start'] != null) {
            $result .= $this->_tagBuilder($navTemplate['mega_menu_wrapper_end']);
        } elseif ($level > 1) {
            if ($navTemplate['navigation_sub_menu_wrapper_start'] != '') {
                $result .= $this->_tagBuilder($navTemplate['navigation_sub_menu_wrapper_end']);
            } else {
                $result .= $navTemplate['navigation_wrapper_end'];
            }
        }
        return $result;
    }

    protected function _listItemStartBuilder($data, $navTemplate, $level, $megaMenu)
    {
        $beginningPart = '';
        $endingPart = '';
        $megamenu_ending = '';
        $linkBuilder = $this->_linkBuilder($data, $navTemplate);
        $activeClass = $linkBuilder['active_class'];
        $linkBeginning = $linkBuilder['link_beginning'];
        $linkEnding = $linkBuilder['link_ending'];
        // full-left/full-right/top-left/top-right/bottom-left/bottom-right/text-left/text-right
        if ($data['beginning_text'] != null) {
            $beginningPart .= $navTemplate['navigation_item_beginning_wrapper_start'];
            if ($navTemplate['navigation_item_icon_position'] == 'top-left' &&
                $navTemplate['navigation_item_icon_wrapper_start'] != null &&
                $data['icon'] != null
            ) {
                $beginningPart .= $this->_tagBuilder($navTemplate['navigation_item_icon_wrapper_start'], $data['icon']) . $navTemplate['navigation_item_icon_wrapper_end'];
            }
            $beginningPart .= $data['beginning_text'];
            $beginningPart .= $navTemplate['navigation_item_beginning_wrapper_end'];
            if ($navTemplate['navigation_item_icon_position'] == 'top-right' &&
                $navTemplate['navigation_item_icon_wrapper_start'] != null &&
                $data['icon'] != null
            ) {
                $beginningPart .= $this->_tagBuilder($navTemplate['navigation_item_icon_wrapper_start'], $data['icon']) . $navTemplate['navigation_item_icon_wrapper_end'];
            }
        }
        if ($data['ending_text'] != null) {
            $endingPart .= $navTemplate['navigation_item_ending_wrapper_start'];
            if ($navTemplate['navigation_item_icon_position'] == 'bottom-left' &&
                $navTemplate['navigation_item_icon_wrapper_start'] != null &&
                $data['icon'] != null
            ) {
                $endingPart .= $this->_tagBuilder($navTemplate['navigation_item_icon_wrapper_start'], $data['icon']) . $navTemplate['navigation_item_icon_wrapper_end'];
            }
            $endingPart .= $data['ending_text'];
            $endingPart .= $navTemplate['navigation_item_ending_wrapper_end'];
            if ($navTemplate['navigation_item_icon_position'] == 'bottom-right' &&
                $navTemplate['navigation_item_icon_wrapper_start'] != null &&
                $data['icon'] != null
            ) {
                $endingPart .= $this->_tagBuilder($navTemplate['navigation_item_icon_wrapper_start'], $data['icon']) . $navTemplate['navigation_item_icon_wrapper_end'];
            }
        }
        if ($level == 2 && $megaMenu == 1 && $navTemplate['mega_menu_section_wrapper_start'] != null) {
            $mainTag = $this->_tagBuilder($navTemplate['mega_menu_section_wrapper_start'], $data['class'], $activeClass) . '<div class="megamenu-header">';
            $megamenu_ending = '</div>';
        } else {
            if ($level > 1 && $navTemplate['navigation_item_wrapper_in_sub_menu_start'] != null) {
                $mainTag = $this->_tagBuilder($navTemplate['navigation_item_wrapper_in_sub_menu_start'], $data['class'], $activeClass);
            } else {
                $mainTag = $this->_tagBuilder($navTemplate['navigation_item_wrapper_start'], $data['class'], $activeClass);
            }
        }
        if (
            $navTemplate['navigation_item_icon_position'] == 'text-right' &&
            $navTemplate['navigation_item_icon_wrapper_start'] != null &&
            $data['icon'] != null
        ) {
            $output = $mainTag . $linkBeginning . $beginningPart . $navTemplate['navigation_item_text_wrapper_start'] . $data['name'] . $navTemplate['navigation_item_text_wrapper_end'] . $this->_tagBuilder($navTemplate['navigation_item_icon_wrapper_start'], $data['icon']) . $navTemplate['navigation_item_icon_wrapper_end'] . $endingPart . $linkEnding . $megamenu_ending;
        } elseif (
            $navTemplate['navigation_item_icon_position'] == 'text-left' &&
            $navTemplate['navigation_item_icon_wrapper_start'] != null &&
            $data['icon'] != null
        ) {
            $output = $mainTag . $linkBeginning . $beginningPart . $this->_tagBuilder($navTemplate['navigation_item_icon_wrapper_start'], $data['icon']) . $navTemplate['navigation_item_icon_wrapper_end'] . $navTemplate['navigation_item_text_wrapper_start'] . $data['name'] . $navTemplate['navigation_item_text_wrapper_end'] . $endingPart . $linkEnding . $megamenu_ending;
        } elseif (
            $navTemplate['navigation_item_icon_position'] == 'full-right' &&
            $navTemplate['navigation_item_icon_wrapper_start'] != null &&
            $data['icon'] != null
        ) {
            $output = $mainTag . $linkBeginning . $beginningPart . $navTemplate['navigation_item_text_wrapper_start'] . $data['name'] . $navTemplate['navigation_item_text_wrapper_end'] . $endingPart . $this->_tagBuilder($navTemplate['navigation_item_icon_wrapper_start'], $data['icon']) . $navTemplate['navigation_item_icon_wrapper_end'] . $linkEnding . $megamenu_ending;
        } elseif (
            $navTemplate['navigation_item_icon_position'] == 'full-left' &&
            $navTemplate['navigation_item_icon_wrapper_start'] != null &&
            $data['icon'] != null
        ) {
            $output = $mainTag . $linkBeginning . $this->_tagBuilder($navTemplate['navigation_item_icon_wrapper_start'], $data['icon']) . $navTemplate['navigation_item_icon_wrapper_end'] . $beginningPart . $navTemplate['navigation_item_text_wrapper_start'] . $data['name'] . $navTemplate['navigation_item_text_wrapper_end'] . $endingPart . $linkEnding . $megamenu_ending;
        } else {
            $output = $mainTag . $linkBeginning . $beginningPart . $data['name'] . $endingPart . $linkEnding . $megamenu_ending;
        }
        return $output;
    }

    // For single nav use

    protected function _linkBuilder($dbData, $navTemplate)
    {
        $path = $navTemplate['navigation_item_no_link_text'];
        if ($dbData['route'] != '') {
            $path = route($dbData['route']);
        } else {
            if (strpos($dbData['custom_link'], 'http://') === 0 || strpos($dbData['custom_link'], 'https://') === 0) {
                if (strpos($dbData['custom_link'], '.') >= 8) {
                    $path = $dbData['custom_link'];
                }
            } elseif (strpos($dbData['custom_link'], 'www.') === 0) {
                $path = 'http://' . $dbData['custom_link'];
            } elseif ($dbData['custom_link'] == 'javascript:;') {
                $path = $dbData['custom_link'];
            } else {
                $path = asset($dbData['custom_link']);
            }
        }
        $activeClass = $navTemplate['navigation_item_link_active_class'] == '' ? 'link-active' : $navTemplate['navigation_item_link_active_class'];
        $activeClass = url()->current() == $path ? $activeClass : '';
        $class = '';

        if ($navTemplate['navigation_item_active_class_on_anchor_tag'] === true && $activeClass !== '') {
            $class = $activeClass;
        }
        if ($class == '' && $navTemplate['navigation_item_link_class'] != null) {
            $class = $navTemplate['navigation_item_link_class'];
        } elseif ($class != '' && $navTemplate['navigation_item_link_class'] != null) {
            $class = $class . ' ' . $navTemplate['navigation_item_link_class'];
        }

        $blank = $dbData['new_tab'] == 1 ? ' target="_blank"' : '';
        $linkBeginning = '<a href ="' . $path . '" class="' . $class . '"' . $blank . '>';
        $linkEnding = '</a>';
        if ($navTemplate['navigation_item_active_class_on_anchor_tag'] === true) {
            $activeClass = '';
        }
        return ['link_beginning' => $linkBeginning, 'link_ending' => $linkEnding, 'active_class' => $activeClass];
    }

    //--------------- backend navigation functions ------------------------

    public function backendMenuBuilder($slug)
    {
        $data['navigationPlaces'] = config('navigation.registered_place');
        $data['slug'] = empty($slug) ? $data['navigationPlaces'][0] : $slug;
        $data['allRoutes'] = \Route::getRoutes()->getRoutesByMethod()['GET'];
        $data['menuItems'] = $this->navigation->getBySlug($data['slug']);
        $data['currentTemplate'] = '';
        $data['menu'] = '<ol class="mymenu">';
        if ($data['menuItems']) {
            $data['menu'] .= $this->backendInnerMenu($data['menuItems']->navigation_items);
            $data['currentTemplate'] = $data['menuItems']->template;
        }
        $data['menu'] .= '</ol>';
        return $data;
    }

    protected function backendInnerMenu($dbData, $parentId = 0, $result = NULL)
    {
        foreach ($dbData as $row) {
            $count = 0;
            if ($row['parent_id'] == $parentId) {
                $ol = FALSE;
                $parentOrder = $row['order'];
                $result .= view('backend.renderable_template._backend_navigation', ['row' => $row])->render();
                foreach ($dbData as $rowInside) {
                    if ($rowInside['parent_id'] == $parentOrder && $count < 1) {
                        if ($ol == FALSE) {
                            $result .= '<ol>';
                            $ol = TRUE;
                        }
                        $count++;
                        $result .= $this->backendInnerMenu($dbData, $parentOrder);
                    }
                }
                if ($ol == TRUE) {
                    $result .= '</ol>';
                    $ol == FALSE;
                }
                $result .= '</li>';
            }
        }
        return $result;
    }

    public function backendMenuSave(Request $request, $slug)
    {
        if (!in_array($slug, config('navigation.registered_place'))) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('The navigation slug is invalid')
            ];
        }
        $menuItems = $request->menu_item;
        if (empty($menuItems) || !is_array($menuItems)) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('There is no item in the menu')
            ];
        }
        $reorderedMenuItems = array_values($menuItems);
        $data = [
            'navigation_items' => $reorderedMenuItems
        ];
        $availableNavigation = $this->navigation->getBySlug($slug);
        if ($availableNavigation) {
            $conditions = ['id' => $availableNavigation->id];
            $output = $this->navigation->updateByConditions($data, $conditions);
        } else {
            $data['slug'] = $slug;
            $output = $this->navigation->create($data);
        }
        if ($output) {
            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __('Menu has been saved successfully')
            ];
        }
        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Menu can not be saved')
        ];
    }
}