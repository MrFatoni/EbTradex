<?php
/**
 * Created by PhpStorm.
 * User: zahid
 * Date: 2018-07-28
 * Time: 4:53 PM
 */

namespace App\Services\Core;

use App\Repositories\Core\Interfaces\AdminSettingInterface;
use Illuminate\Http\Request;

class AdminSettingService
{
    protected $dbData = [];
    protected $adminSetting;

    public function __construct(AdminSettingInterface $adminSetting)
    {
        $this->adminSetting = $adminSetting;
    }

    public function adminUpdate(Request $request, $adminSettingType = null)
    {
        $input = $request->adminSettings;
        $updatableAttributes = [];
        $imageUploadCount = 0;
        $newImageUploadCount = 0;
        $dataconfig = is_null($adminSettingType) ? config("adminsetting.settings") : config("adminsetting.settings.{$adminSettingType}");
        $rules = [];
        $existingSettingsFromDatabase = $this->adminSetting->getBySlugs(array_keys($input));
        if (is_null($adminSettingType)) {
            foreach ($dataconfig as $dc) {
                $rules = array_merge($rules, $dc);
            }
        } else {
            $rules = $dataconfig;
        }
        foreach ($input as $key => $val) {
            if (!in_array($key, array_keys($rules))) {
                unset($input[$key]);
                continue;
            }
            if (in_array($rules[$key]['field_type'], ['checkbox'])) {
                foreach ($val as $childkey => $childValue) {
                    if (!$this->_adminSettingValidation($key, $childValue, $rules)) {
                        unset($input[$key][$childkey]);
                        continue;
                    }
                    $input[$key][$childkey] = strip_tags($childValue, '<h1><h2><h3><h4><h5><h6><hr><article><section><video><audio><table><tr><td><thead><tfoot><footer><header><p><br><b><i><u><strong><ul><ol><dl><dt><li><div><sub><sup><span><img><a>');
                }
                if (empty($input[$key])) {
                    unset($input[$key]);
                    continue;
                }
                $val = json_encode($val);
            } elseif ( in_array($rules[$key]['field_type'], ['image']) && !empty($val) ) {
                if (!$this->_adminSettingValidation($key, $val, $rules)) {
                    unset($input[$key]);
                    continue;
                }

                $filePath = config('commonconfig.path_image');
                $image = app(FileUploadService::class)->upload($val, $filePath, $key, $prefix = '');

                if (empty($image)) {
                    unset($input[$key]);
                    continue;
                }

                if(array_key_exists($key, $existingSettingsFromDatabase)){
                    $imageUploadCount +=1;
                }else{
                    $newImageUploadCount +=1;
                }

                $input[$key] = $image;
                $val = $image;

            } else {
                if (!$this->_adminSettingValidation($key, $val, $rules)) {
                    unset($input[$key]);
                    continue;
                }
                $input[$key] = strip_tags($val, '<h1><h2><h3><h4><h5><h6><hr><article><section><video><audio><table><tr><td><thead><tfoot><footer><header><p><br><b><i><u><strong><ul><ol><dl><dt><li><div><sub><sup><span><img><a>');
            }
            $updatableAttributes[$key] = [
                'conditions' => ['slug' => $key],
                'fields' => [
                    'value' => $val
                ]
            ];
        }


        $newSettings = array_diff_key($input, $existingSettingsFromDatabase);
        $newSettingToInsert = [];
        $newInsertable = count($newSettings) + $newImageUploadCount;
        $update = 0;
        if ($newInsertable > 0) {
            $date = now();
            foreach ($newSettings as $newKey => $newVal) {
                unset($updatableAttributes[$newKey]);
                $newSettingToInsert[] = [
                    'slug' => $newKey,
                    'value' => $newVal,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
            $inserted = $this->adminSetting->insert($newSettingToInsert);
            if (!$inserted) {
                return [
                    SERVICE_RESPONSE_STATUS => false,
                    SERVICE_RESPONSE_MESSAGE => __('New Settings could not be added!'),
                ];
            }
        }

        if (count($updatableAttributes) > 0) {
            $update = $this->adminSetting->bulkUpdate(array_values($updatableAttributes)) + $imageUploadCount;
            if ($update || $newInsertable > 0) {
                $cacheSetting = cache()->get("admin_settings");
                if (!empty($cacheSetting)) {
                    cache()->forget("admin_settings");
                    $cacheSetting = array_merge($cacheSetting, $input);
                }
                cache()->forever("admin_settings", $cacheSetting);
            }
            $message = $newInsertable > 0 ? __(':updatable setting(s) have been changed! and :newInsertable item(s) have been newly added', ['updatable' => $update, 'newInsertable' => $newInsertable]) : __(':updatable setting(s) have been changed!', ['updatable' => $update]);
            if ($newInsertable > 0 || $update > 0) {
                return [
                    SERVICE_RESPONSE_STATUS => true,
                    SERVICE_RESPONSE_MESSAGE => $message,
                ];
            }
        } elseif ($newInsertable > 0) {
            $cacheSetting = cache()->get("admin_settings");
            if (!empty($cacheSetting)) {
                cache()->forget("admin_settings");
                $cacheSetting = array_merge($cacheSetting, $newSettings);
            }
            cache()->forever("admin_settings", $cacheSetting);
            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __(':newInsertable item(s) have been newly added', ['newInsertable' => $newInsertable]),
            ];
        }
        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('There is nothing to be changed!'),
        ];
    }

    protected function _adminSettingValidation($key, $val, $rules)
    {
        if (isset($rules[$key]['data_type'])) {
            if (
                ($rules[$key]['data_type'] == 'numeric' && !is_numeric($val)) ||
                ($rules[$key]['data_type'] == 'integer' && filter_var($val, FILTER_VALIDATE_INT) == false) ||
                ($rules[$key]['data_type'] == 'email' && filter_var($val, FILTER_VALIDATE_EMAIL) == false) ||
                ($rules[$key]['data_type'] == 'digit' && ctype_digit($val) == false) ||
                ($rules[$key]['data_type'] == 'image' && !empty($val) && !$this->_imageValidation($val)) ||
                ($rules[$key]['data_type'] == 'required' && $val == '')
            ) {
                return false;
            }
        }
        if ((isset($rules[$key]['min']) && $rules[$key]['min'] > $val) || (isset($rules[$key]['max']) && $rules[$key]['max'] < $val)) {
            return false;
        }
        if (isset($rules[$key]['type_database']) && (!isset($rules[$key]['data_array']))) {
            if (!isset($this->dbData[$key])) {
                $this->dbData[$key] = app($rules[$key]['data_array'][0])->{$rules[$key]['data_array'][0]}()->toArray();
            }
            if (!in_array($val, array_keys($this->dbData[$key]))) {
                return false;
            }
        } elseif (isset($rules[$key]['type_database']) && isset($rules[$key]['data_array'])) {
            if (!isset($this->dbData[$key])) {
                $this->dbData[$key] = app($rules[$key]['data_array'][0])->{$rules[$key]['data_array'][1]}()->toArray();
            }
            if (!in_array($val, array_keys($this->dbData[$key]))) {
                return false;
            }
        } elseif (isset($rules[$key]['previous']) && !in_array($val, array_keys($this->dbData[$rules[$key]['previous']]))) {
            return false;
        } elseif (!isset($rules[$key]['type_function']) && isset($rules[$key]['data_array'])) {
            if (!is_array($rules[$key]['data_array']) || !in_array($val, array_keys($rules[$key]['data_array']))) {
                return false;
            }
        }
        return true;
    }

    private function _imageValidation($image)
    {
        if (in_array($image->getMimeType(), ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'])) {
            return true;
        }
        return false;
    }

    public function adminForm($data = null, $viewOnly = false)
    {
        $dbData = [];
        $output = '';
        $dataconfig = config('adminsetting.settings');
        if (empty($data)) {
            $data = [];
            foreach ($dataconfig as $dc) {
                $data = array_merge($data, $dc);
            }
        } elseif (is_array($data)) {
            $data = array_intersect_key($dataconfig, array_flip($data));
        } else {
            $data = config('adminsetting.settings.' . $data);
        }
        $common_wrapper = config('adminsetting.common_wrapper');
        foreach ($data as $key => $value) {
            $common_input_options = config('adminsetting.common_' . $value['field_type'] . '_input_wrapper');
            $db_data = admin_settings($key);
            $input_class = isset($value['input_class']) ? __($value['input_class']) : (isset($common_input_options['input_class']) ? $common_input_options['input_class'] : '');
            $value_data = old($key, $db_data);
            $place_holder = isset($value['placeholder']) ? __($value['placeholder']) : "";
            $data_array = [];
            if (isset($value['type_function']) && isset($value['data_array'])) {
                $data_array = call_user_func_array($value['data_array'], []);
            } elseif (isset($value['type_database']) && isset($value['data_array'])) {
                $data_array = app($value['data_array'][0])->{$value['data_array'][1]}()->toArray();
                $dbData[$key] = $data_array;
            } elseif (isset($value['previous'])) {
                $data_array = $dbData[$value['previous']];
            } elseif (isset($value['data_array'])) {
                $data_array = $value['data_array'];
            }
            $input_start_tag = isset($value['input_start_tag']) ? $value['input_start_tag'] : (isset($common_input_options['input_start_tag']) ? $common_input_options['input_start_tag'] : '');
            $input_end_tag = isset($value['input_end_tag']) ? $value['input_end_tag'] : (isset($common_input_options['input_end_tag']) ? $common_input_options['input_end_tag'] : '');
            $output .= isset($key['section_start_tag']) ? $key['section_start_tag'] : (isset($common_wrapper['section_start_tag']) ? $common_wrapper['section_start_tag'] : '');
            $output .= isset($key['slug_start_tag']) ? $key['slug_start_tag'] : (isset($common_wrapper['slug_start_tag']) ? $common_wrapper['slug_start_tag'] : '');
            $output .= __($value['slug_text']);
            $output .= isset($value['slug_end_tag']) ? $value['slug_end_tag'] : (isset($common_wrapper['slug_end_tag']) ? $common_wrapper['slug_end_tag'] : '');
            $output .= isset($value['value_start_tag']) ? $value['value_start_tag'] : (isset($common_wrapper['value_start_tag']) ? $common_wrapper['value_start_tag'] : '');
            if ($viewOnly) {
                $output .= $this->_viewOutput($key, $value['field_type'], $data_array, $value_data);
            } else {
                $output .= $this->{'_' . $value['field_type']}($key, $data_array, $input_class, $value_data, $place_holder, $input_start_tag, $input_end_tag);
            }
            $output .= isset($value['value_end_tag']) ? $value['value_end_tag'] : (isset($common_wrapper['value_end_tag']) ? $common_wrapper['value_end_tag'] : '');
            $output .= isset($value['section_end_tag']) ? $value['section_end_tag'] : (isset($common_wrapper['section_end_tag']) ? $common_wrapper['section_end_tag'] : '');
        }
        return ['html' => $output, 'settingSections' => array_keys($dataconfig)];
    }

    private function _viewOutput($key, $value, $data_array, $value_data)
    {
        if (in_array($value, ['checkbox'])) {
            if (is_json($value_data)) {
                $value_data = json_decode($value_data, true);
                $output = implode(', ', array_intersect_key($data_array, array_flip($value_data)));
                return !empty($output) ? $output : $value_data;
            } elseif (is_array($value_data)) {
                return implode(', ', array_intersect_key($data_array, array_flip($value_data)));
            } elseif (!empty($data_array)) {
                return isset($data_array[$value_data]) ? $data_array[$value_data] : $value_data;
            } else {
                return $value_data;
            }
        } elseif (in_array($value, ['image'])) {
            return '<img width="100" src="' . get_image($value_data) . '" />';
        } elseif (!empty($data_array)) {
            return isset($data_array[$value_data]) ? $data_array[$value_data] : $value_data;
        } else {
            return $value_data;
        }
    }

    private function _text($key, $data_array, $input_class, $value_data, $place_holder, $input_start_tag, $input_end_tag)
    {
        return $input_start_tag . '<input class="' . $input_class . '" type="text" value="' . $value_data . '" name="adminSettings[' . $key . ']" placeholder="' . $place_holder . '">' . $input_end_tag;
    }

    //rules here

    private function _image($key, $data_array, $input_class, $value_data, $place_holder, $input_start_tag, $input_end_tag)
    {
        $output = $input_start_tag . '<input class="' . $input_class . '" type="file" name="adminSettings[' . $key . ']" placeholder="' . $place_holder . '">';
        if (empty($value_data)) {
            $output = $output . $input_end_tag;
        }else{
            $output = $output . '<img width="100" style="margin-top:15px" src="' . get_image($value_data) . '" />' . $input_end_tag;
        }
        return $output;
    }

    private function _textarea($key, $data_array, $input_class, $value_data, $place_holder, $input_start_tag, $input_end_tag)
    {
        return $input_start_tag . '<textarea class="' . $input_class . '" name="adminSettings[' . $key . ']" placeholder="' . $place_holder . '">' . $value_data . '</textarea>' . $input_end_tag;
    }

    //rules here

    private function _select($key, $data_array, $input_class, $value_data, $place_holder, $input_start_tag, $input_end_tag)
    {
        $output = $input_start_tag . '<select class="' . $input_class . '" name="adminSettings[' . $key . ']">';
        foreach ($data_array as $datakey => $dataval) {
            $output .= '<option value="' . $datakey . '"';
            $output .= $datakey == $value_data ? " selected" : "";
            $output .= '>' . $dataval . '</option>';
        }
        $output .= '</select>' . $input_end_tag;
        return $output;
    }

    private function _checkbox($key, $data_array, $input_class, $value_data, $place_holder, $input_start_tag, $input_end_tag)
    {
        $output = '';
        if (is_json($value_data)) {
            $value_data = json_decode($value_data, true);
        }
        foreach ($data_array as $datakey => $dataval) {
            $output .= $input_start_tag . '<input id="' . $key . '-' . $datakey . '" class="' . $input_class . '" type="checkbox" name="adminSettings[' . $key . '][]"  value="' . $datakey . '"';
            $output .= is_array($value_data) && in_array($datakey, $value_data) ? " checked" : "";
            $output .= '> <span><label for="' . $key . '-' . $datakey . '">' . $dataval . '</label></span>' . $input_end_tag;
        }
        return $output;
    }

    private function _radio($key, $data_array, $input_class, $value_data, $place_holder, $input_start_tag, $input_end_tag)
    {
        $output = '';
        foreach ($data_array as $datakey => $dataval) {
            $output .= $input_start_tag . '<input id="' . $key . '-' . $datakey . '" class="' . $input_class . '" type="radio" name="adminSettings[' . $key . ']" value="' . $datakey . '"';
            $output .= $datakey == $value_data ? " checked" : "";
            $output .= '> <span><label for="' . $key . '-' . $datakey . '">' . $dataval . '</label></span>' . $input_end_tag;
        }
        return $output;
    }

    private function _toggle($key, $data_array, $input_class, $value_data, $place_holder, $input_start_tag, $input_end_tag)
    {
        if (count($data_array) != 2) {
            return '';
        }
        $array_keys = array_keys($data_array);
        if (!ctype_digit(implode('', $array_keys))) {
            return '';
        }
        ksort($data_array);
        if (!in_array($value_data, $array_keys)) {
            $value_data = $array_keys[0];
        }
        $output = $input_start_tag . '<div class="cm-switch">';
        $class = 'cm-switch-label cm-switch-label-off';
        $value = '&#10006;';
        foreach ($data_array as $datakey => $dataval) {
            $output .= '<input id="' . $key . '-' . $datakey . '" class="cm-switch-input ' . $input_class . '" type="radio" name="adminSettings[' . $key . ']" value="' . $datakey . '"';
            $output .= $datakey == $value_data ? " checked>" : ">";
            $output .= '<label for="' . $key . '-' . $datakey . '" class="' . $class . '">' . $value . '</label>';
            $class = 'cm-switch-label cm-switch-label-on';
            $value = '&#x2714;';
        }
        $output .= '<span class="cm-switch-selection"></span>' . $input_end_tag . '</div>';
        return $output;
    }
}
