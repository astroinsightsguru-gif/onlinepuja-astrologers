@extends('../layout/' . $layout)

@section('subhead')
    <title>Settings</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <form method="POST" enctype="multipart/form-data" id="edit-form">
        @csrf
        <h2 class="d-inline intro-y text-lg font-medium mt-10">Settings</h2>
        <button type="submit" class="btn btn-primary shadow-md mr-2 d-inline addbtn mt-10">Save</button>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible"></div>
        </div>
        <div id="link-tab" class="p-3">
            <ul class="nav nav-link-tabs" role="tablist">
                @foreach ($flagGroup as $group)
                    <li id="{{ $loop->index }}" class="nav-item flex-1 {{ $loop->first ? 'active' : '' }}" role="presentation">
                        <button class="nav-link w-full py-2 {{ $loop->first ? 'active' : '' }}"
                            data-tw-toggle="pill"
                            data-tw-target="#{{ $group->flagGroupName }}"
                            type="button" role="tab"
                            aria-controls="{{ $group->flagGroupName }}"
                            aria-selected="true">
                            {{ $group->flagGroupName }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="setting tab-content mt-5 mastertab">
                @foreach ($flagGroup as $groupIndex => $group)
                    <div id="{{ $group->flagGroupName }}"
                        class="tab-pane leading-relaxed {{ $loop->first ? 'active' : '' }}"
                        role="tabpanel"
                        aria-labelledby="example-1-tab">

                        {{-- ── MAIN GROUP SYSTEM FLAGS ── --}}
                        @if (count($group->systemFlag) > 0)
                            @foreach ($group->systemFlag as $systemFlagIndex => $systemFlag)

                                {{-- TEXT --}}
                                @if ($systemFlag->valueType == 'Text')
                                    @if ($systemFlag->name == 'appDesignId')
                                        <input type="hidden" name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]" value="{{ $systemFlag->name }}">
                                        <input type="hidden" name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]" value="{{ $systemFlag->value }}">
                                    @else
                                        <div>
                                            <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                                {{ $systemFlag->displayName }}
                                                @if ($systemFlag->description)
                                                    <a class="systooltip">
                                                        <i class="fa fa-info-circle w-4 h-4 ml-1" style="margin-top:4px"></i>
                                                        <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                    </a>
                                                @endif
                                            </label>
                                            <input type="hidden" name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]" value="{{ $systemFlag->name }}">
                                            <input onkeypress="return validateJavascript(event);" type="text"
                                                name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                class="form-control" value="{{ $systemFlag->value }}">
                                        </div>
                                    @endif
                                @endif

                                {{-- APP VERSION --}}
                                @if ($systemFlag->name == 'AppVersion')
                                    <div>
                                        <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip">
                                                    <i class="fa fa-info-circle w-4 h-4 ml-1" style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden" name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]" value="{{ $systemFlag->name }}">
                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                            class="form-control" value="{{ $systemFlag->value }}">
                                    </div>
                                @endif

                                {{-- NUMBER --}}
                                @if ($systemFlag->valueType == 'Number')
                                    <div>
                                        <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip">
                                                    <i class="fa fa-info-circle w-4 h-4 ml-1" style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden" name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]" value="{{ $systemFlag->name }}">
                                        <input type="number"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                            class="form-control" value="{{ $systemFlag->value }}">
                                    </div>
                                @endif

                                {{-- RADIO --}}
                                @if ($systemFlag->valueType == 'Radio')
                                    <div>
                                        <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip">
                                                    <i class="fa fa-info-circle w-4 h-4 ml-1" style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden" name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]" value="{{ $systemFlag->name }}">

                                        @if ($systemFlag->name == 'FirstFreeChat')
                                            <div class="flex flex-col sm:flex-row mt-2">
                                                <div class="form-check mr-2">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='1' {{ $systemFlag->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='0' {{ $systemFlag->value == '0' ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($systemFlag->name == 'AiAstrologer')
                                            <div class="flex flex-col sm:flex-row mt-2">
                                                <div class="form-check mr-2">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='1' {{ $systemFlag->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='0' {{ $systemFlag->value == '0' ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($systemFlag->name == 'FirstFreeChatRecharge')
                                            <div class="flex flex-col sm:flex-row mt-2">
                                                <div class="form-check mr-2">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='1' {{ $systemFlag->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='0' {{ $systemFlag->value == '0' ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($systemFlag->name == 'Callsection' || $systemFlag->name == 'Chatsection' || $systemFlag->name == 'Livesection')
                                            <div class="flex flex-row mt-2">
                                                <span class="form-check mr-2">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='1' {{ $systemFlag->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </span>
                                                <span class="form-check mr-2">
                                                    <input class="form-check-input" type="radio"
                                                        name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                        value='0' {{ $systemFlag->value == '0' ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- FILE --}}
                                @if ($systemFlag->valueType == 'File')
                                    <div class="intro-y col-span-12 sm:col-span-6 2xl:col-span-4 xl:col-span-4 d-inline">
                                        <div class="box p-5 mt-2 text-center">
                                            <label class="form-label w-full mt-2">{{ $systemFlag->displayName }}</label>
                                            <input type="hidden"
                                                name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                                value="{{ $systemFlag->valueType }}">
                                            <input type="hidden"
                                                name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                                value="{{ $systemFlag->name }}">
                                            <div class="settingimg">
                                                <img id="{{ $systemFlag->name }}" src="/{{ $systemFlag->value }}" width="150px" alt="gift">
                                            </div>
                                            <div>
                                                <input type="file" class="mt-2"
                                                    name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                    onchange="previews('{{ $systemFlag->name }}')"
                                                    accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- MULTISELECT --}}
                                @if ($systemFlag->valueType == 'MultiSelect')
                                    <div>
                                        <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip">
                                                    <i class="fa fa-info-circle w-4 h-4 ml-1" style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                            value="{{ $systemFlag->name }}">
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                            value="{{ $systemFlag->valueType }}">
                                        <select name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value][]"
                                            class="form-control select2 language" multiple data-placeholder="Choose Language">
                                            @foreach ($language as $lan)
                                                <option value="{{ $lan->id }}">{{ $lan->languageName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                {{-- SELECT WALLET TYPE --}}
                                @if ($systemFlag->valueType == 'SelectWalletType')
                                    <div>
                                        <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip">
                                                    <i class="fa fa-info-circle w-4 h-4 ml-1" style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                            value="{{ $systemFlag->name }}">
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                            value="{{ $systemFlag->valueType }}">
                                        <select name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                            class="form-control" {{ !empty($systemFlag->value) ? 'disabled' : '' }}
                                            data-placeholder="Choose Wallet">
                                            <option value="">Choose Wallet</option>
                                            <option value="Wallet" {{ $systemFlag->value == 'Wallet' ? 'selected' : '' }}>Wallet</option>
                                            <option value="Coin" {{ $systemFlag->value == 'Coin' ? 'selected' : '' }}>Coin</option>
                                        </select>
                                    </div>
                                @endif

                                {{-- MULTI SELECT WEB LANG --}}
                                @if ($systemFlag->valueType == 'MultiSelectWebLang')
                                    @php $selectedLanguages = json_decode($systemFlag->value, true) ?: []; @endphp
                                    <div>
                                        <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                            {{ $systemFlag->displayName }}
                                            @if ($systemFlag->description)
                                                <a class="systooltip">
                                                    <i class="fa fa-info-circle w-4 h-4 ml-1" style="margin-top:4px"></i>
                                                    <span class="tooltiptext">{{ $systemFlag->description }}</span>
                                                </a>
                                            @endif
                                        </label>
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                            value="{{ $systemFlag->name }}">
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                            value="{{ $systemFlag->valueType }}">
                                        <select name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value][]"
                                            class="form-control select2" multiple data-placeholder="Choose Language">
                                            @foreach ($language as $lan)
                                                <option value="{{ $lan->languageCode }}" {{ in_array($lan->languageCode, $selectedLanguages) ? 'selected' : '' }}>
                                                    {{ $lan->languageName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                {{-- VIDEO (BehindScenes - main group) --}}
                                @if ($systemFlag->valueType == 'Video' && $systemFlag->name == 'BehindScenes')
                                    @php $hasVideo = !empty($systemFlag->value); @endphp
                                    <div>
                                        <label class="form-label mt-2">{{ $systemFlag->displayName }}</label>

                                        {{-- valueType & name hidden inputs --}}
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][valueType]"
                                            value="{{ $systemFlag->valueType }}">
                                        <input type="hidden"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][name]"
                                            value="{{ $systemFlag->name }}">

                                        {{-- Hidden input to preserve existing value when no new file uploaded --}}
                                        <input type="hidden"
                                            id="hiddenVideoInput_{{ $loop->index }}"
                                            name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                            value="{{ $systemFlag->value }}">

                                        {{-- Enable / Disable toggle --}}
                                        <div>
                                            <label>
                                                <input class="form-check-input" type="radio"
                                                    name="video_toggle_{{ $loop->index }}"
                                                    value="enable"
                                                    onclick="toggleVideoUpload({{ $loop->index }}, true)"
                                                    {{ $hasVideo ? 'checked' : '' }}> Enable
                                            </label>
                                            <label>
                                                <input class="form-check-input" type="radio"
                                                    name="video_toggle_{{ $loop->index }}"
                                                    value="disable"
                                                    onclick="toggleVideoUpload({{ $loop->index }}, false)"
                                                    {{ !$hasVideo ? 'checked' : '' }}> Disable
                                            </label>
                                        </div>

                                        {{-- Video preview & file input (no name — file appended via JS on submit) --}}
                                        <div id="videoSection_{{ $loop->index }}"
                                            style="{{ $hasVideo ? 'display:block;' : 'display:none;' }}">
                                            <video controls id="editMyVideo_{{ $loop->index }}"
                                                style="width:150px;object-fit:cover" preload="metadata">
                                                <source id="blogvideo_{{ $loop->index }}" type="video/mp4"
                                                    src="/{{ $systemFlag->value }}">
                                                <track label="English" kind="subtitles" srclang="en" default />
                                            </video>
                                            {{-- No name attribute: file appended manually in FormData on submit --}}
                                            <input type="file"
                                                id="blogImage_{{ $loop->index }}"
                                                data-field-name="group[{{ $groupIndex }}][systemFlag][{{ $loop->index }}][value]"
                                                data-hidden-id="hiddenVideoInput_{{ $loop->index }}"
                                                onchange="editVideoPreviews({{ $loop->index }})"
                                                accept="video/mp4">
                                        </div>
                                    </div>
                                @endif

                            @endforeach
                        @endif

                        {{-- ── SUBGROUPS ── --}}
                        @if (count($group->subGroup) > 0)
                            @foreach ($group->subGroup as $subGroupIndex => $subGroup)

                                <h4 class="my-4 text-lg font-medium {{ strtolower(str_replace(' ', '_', $subGroup->flagGroupName)) }}">
                                    {{ ucwords($subGroup->flagGroupName) }}
                                    @if ($subGroup->description)
                                        <a class="systooltip">
                                            <i class="fa fa-info-circle w-4 h-4 ml-1" style="margin-top:4px"></i>
                                            <span class="tooltiptext">{{ $subGroup->description }}</span>
                                        </a>
                                    @endif
                                </h4>

                                @if ($subGroup->parentFlagGroupId == 2 || $subGroup->id == 7 || $subGroup->id == 65 || $subGroup->id == 66)
                                    <div class="mb-2">
                                        <input type="hidden" value="{{ $subGroup->id }}" name="flaggroups[{{ $subGroup->id }}][id]">
                                        <label>
                                            <input class="form-check-input" type="radio"
                                                name="flaggroups[{{ $subGroup->id }}][isActive]"
                                                value="1" {{ $subGroup->isActive ? 'checked' : '' }}> Enable
                                        </label>
                                        <label>
                                            <input class="form-check-input" type="radio"
                                                name="flaggroups[{{ $subGroup->id }}][isActive]"
                                                value="0" {{ !$subGroup->isActive ? 'checked' : '' }}> Disable
                                        </label>
                                    </div>
                                @endif

                                <div class="box p-3 {{ strtolower(str_replace(' ', '_', $subGroup->flagGroupName)) }}">

                                    @if ($subGroup->flagGroupName == 'AstrologyAPI')
                                        <select name="astroApiCallType" id="astroApiCallType">
                                            <option value="3" {{ $astroApiCallType == 3 ? 'selected' : '' }}>Vedic Astro API</option>
                                        </select>
                                    @endif

                                    @foreach ($subGroup->systemFlag as $systemFlagInd => $systemFlag)

                                        {{-- TEXT --}}
                                        @if ($systemFlag->valueType == 'Text')
                                            @if ($systemFlag->name != 'AstrologyApiUserId' && $systemFlag->name != 'AstrologyApiKey')
                                                <div>
                                                    <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                                        {{ $systemFlag->displayName }}
                                                    </label>
                                                    <input type="hidden"
                                                        name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]"
                                                        value="{{ $systemFlag->name }}">
                                                    <input onkeypress="return validateJavascript(event);" type="text"
                                                        name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                        class="form-control" value="{{ $systemFlag->value }}">
                                                </div>
                                            @endif
                                        @endif

                                        {{-- NUMBER --}}
                                        @if ($systemFlag->valueType == 'Number')
                                            <div>
                                                <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                                    {{ $systemFlag->displayName }}
                                                </label>
                                                <input type="hidden"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]"
                                                    value="{{ $systemFlag->name }}">
                                                <input type="number"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                    class="form-control" value="{{ $systemFlag->value }}">
                                            </div>
                                        @endif

                                        {{-- RADIO --}}
                                        @if ($systemFlag->valueType == 'Radio')
                                            <div>
                                                <label class="form-label w-full flex flex-col sm:flex-row mt-2">
                                                    {{ $systemFlag->displayName }}
                                                </label>
                                                <input type="hidden"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]"
                                                    value="{{ $systemFlag->name }}">

                                                @if ($groupIndex == 3)
                                                    @if ($systemFlag->name == 'storege_provider')
                                                        <div class="flex flex-col sm:flex-row mt-2">
                                                            <div class="form-check mr-2">
                                                                <input class="form-check-input bucket_radio" type="radio"
                                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                                    value='google_bucket' {{ $systemFlag->value == 'google_bucket' ? 'checked' : '' }}>
                                                                <label class="form-check-label">Google Bucket</label>
                                                            </div>
                                                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                                                <input class="form-check-input bucket_radio" type="radio"
                                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                                    value='aws_bucket' {{ $systemFlag->value == 'aws_bucket' ? 'checked' : '' }}>
                                                                <label class="form-check-label">AWS Bucket</label>
                                                            </div>
                                                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                                                <input class="form-check-input bucket_radio" type="radio"
                                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                                    value='digital_ocean' {{ $systemFlag->value == 'digital_ocean' ? 'checked' : '' }}>
                                                                <label class="form-check-label">Digital Ocean</label>
                                                            </div>
                                                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                                                <input class="form-check-input bucket_radio" type="radio"
                                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                                    value='local' {{ $systemFlag->value == 'local' ? 'checked' : '' }}>
                                                                <label class="form-check-label">Local Storage</label>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="flex flex-col sm:flex-row mt-2">
                                                            <div class="form-check mr-2">
                                                                <input class="form-check-input streaming_radio" type="radio"
                                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                                    value='agora' {{ $systemFlag->value == 'agora' ? 'checked' : '' }}>
                                                                <label class="form-check-label">Agora</label>
                                                            </div>
                                                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                                                <input class="form-check-input streaming_radio" type="radio"
                                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                                    value='zego' {{ $systemFlag->value == 'zego' ? 'checked' : '' }}>
                                                                <label class="form-check-label">Zegocloud</label>
                                                            </div>
                                                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                                                <input class="form-check-input streaming_radio" type="radio"
                                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                                    value='hms' {{ $systemFlag->value == 'hms' ? 'checked' : '' }}>
                                                                <label class="form-check-label">100ms</label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="flex flex-col sm:flex-row mt-2">
                                                        <div class="form-check mr-2">
                                                            <input class="form-check-input" type="radio"
                                                                name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                                value='RazorPay' {{ $systemFlag->value == 'RazorPay' ? 'checked' : '' }}>
                                                            <label class="form-check-label">Razor Pay</label>
                                                        </div>
                                                        <div class="form-check mr-2 mt-2 sm:mt-0">
                                                            <input class="form-check-input" type="radio"
                                                                name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                                value='Stripe' {{ $systemFlag->value == 'Stripe' ? 'checked' : '' }}>
                                                            <label class="form-check-label">Stripe</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- FILE (subgroup) --}}
                                        @if ($systemFlag->valueType == 'File')
                                            <div class="intro-y col-span-12 sm:col-span-6 2xl:col-span-4 xl:col-span-4 d-inline">
                                                <div class="box p-5 mt-2 text-center">
                                                    <label class="form-label w-full mt-2">{{ $systemFlag->displayName }}</label>
                                                    {{-- FIXED: correct name using $subGroupIndex and $systemFlagInd --}}
                                                    <input type="hidden"
                                                        name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][valueType]"
                                                        value="{{ $systemFlag->valueType }}">
                                                    <input type="hidden"
                                                        name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]"
                                                        value="{{ $systemFlag->name }}">
                                                    <div class="settingimg">
                                                        <img id="{{ $systemFlag->name }}" src="/{{ $systemFlag->value }}" width="150px" alt="gift">
                                                    </div>
                                                    <div>
                                                        <input type="file" class="mt-2"
                                                            name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                            onchange="previews('{{ $systemFlag->name }}')"
                                                            accept="image/*">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- VIDEO (subgroup) --}}
                                        @if ($systemFlag->valueType == 'Video')
                                            @php $hasVideo = !empty($systemFlag->value); @endphp
                                            <div>
                                                <label class="form-label mt-2">{{ $systemFlag->displayName }}</label>

                                                {{-- FIXED: correct name using $subGroupIndex and $systemFlagInd --}}
                                                <input type="hidden"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][valueType]"
                                                    value="{{ $systemFlag->valueType }}">
                                                <input type="hidden"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][name]"
                                                    value="{{ $systemFlag->name }}">

                                                {{-- Hidden input to preserve existing DB value when no new file uploaded --}}
                                                <input type="hidden"
                                                    id="hiddenSubVideoInput_{{ $subGroupIndex }}_{{ $systemFlagInd }}"
                                                    name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                    value="{{ $systemFlag->value }}">

                                                {{-- Enable / Disable toggle --}}
                                                <div>
                                                    <label>
                                                        <input class="form-check-input" type="radio"
                                                            name="sub_video_toggle_{{ $subGroupIndex }}_{{ $systemFlagInd }}"
                                                            value="enable"
                                                            onclick="toggleSubVideoUpload({{ $subGroupIndex }}, {{ $systemFlagInd }}, true)"
                                                            {{ $hasVideo ? 'checked' : '' }}> Enable
                                                    </label>
                                                    <label>
                                                        <input class="form-check-input" type="radio"
                                                            name="sub_video_toggle_{{ $subGroupIndex }}_{{ $systemFlagInd }}"
                                                            value="disable"
                                                            onclick="toggleSubVideoUpload({{ $subGroupIndex }}, {{ $systemFlagInd }}, false)"
                                                            {{ !$hasVideo ? 'checked' : '' }}> Disable
                                                    </label>
                                                </div>

                                                {{-- Video preview & file input (no name — appended via JS on submit) --}}
                                                <div id="subVideoSection_{{ $subGroupIndex }}_{{ $systemFlagInd }}"
                                                    style="{{ $hasVideo ? 'display:block;' : 'display:none;' }}">
                                                    <video controls
                                                        id="subEditMyVideo_{{ $subGroupIndex }}_{{ $systemFlagInd }}"
                                                        style="width:150px;object-fit:cover" preload="metadata">
                                                        <source id="subBlogvideo_{{ $subGroupIndex }}_{{ $systemFlagInd }}"
                                                            type="video/mp4" src="/{{ $systemFlag->value }}">
                                                        <track label="English" kind="subtitles" srclang="en" default />
                                                    </video>
                                                    {{-- No name attr: file is appended manually via FormData on submit --}}
                                                    <input type="file"
                                                        id="subBlogImage_{{ $subGroupIndex }}_{{ $systemFlagInd }}"
                                                        data-field-name="group[{{ $groupIndex }}][subGroup][{{ $subGroupIndex }}][systemFlag][{{ $systemFlagInd }}][value]"
                                                        data-hidden-id="hiddenSubVideoInput_{{ $subGroupIndex }}_{{ $systemFlagInd }}"
                                                        onchange="editSubVideoPreviews({{ $subGroupIndex }}, {{ $systemFlagInd }})"
                                                        accept="video/mp4">
                                                </div>
                                            </div>
                                        @endif

                                    @endforeach
                                </div>
                            @endforeach
                        @endif

                    </div>
                @endforeach
            </div>
        </div>
    </form>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">

    // ── Select2 init ─────────────────────────────────────────────────────────
    $(document).ready(function () {
        jQuery('.select2').select2({
            allowClear: true,
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });

    var flagGroup = {{ Js::from($flagGroup) }};
    var language  = flagGroup.filter(c => c.flagGroupName == 'General');
    language      = language[0].systemFlag.filter(c => c.name == 'Language');
    var languageKnown = language[0].value.split(',');
    $('.language').val(languageKnown).trigger('change');

    // ── Main group video toggle (BehindScenes) ────────────────────────────────
    function toggleVideoUpload(index, enable) {
        var videoSection = document.getElementById('videoSection_' + index);
        var fileInput    = document.getElementById('blogImage_' + index);
        var hiddenInput  = document.getElementById('hiddenVideoInput_' + index);

        if (enable) {
            videoSection.style.display = 'block';
        } else {
            videoSection.style.display = 'none';
            if (fileInput)  fileInput.value  = '';
            if (hiddenInput) hiddenInput.value = ''; // backend else block will load from DB
        }
    }

    // ── SubGroup video toggle ─────────────────────────────────────────────────
    function toggleSubVideoUpload(subGroupIndex, flagInd, enable) {
        var videoSection = document.getElementById('subVideoSection_' + subGroupIndex + '_' + flagInd);
        var fileInput    = document.getElementById('subBlogImage_' + subGroupIndex + '_' + flagInd);
        var hiddenInput  = document.getElementById('hiddenSubVideoInput_' + subGroupIndex + '_' + flagInd);

        if (enable) {
            videoSection.style.display = 'block';
        } else {
            videoSection.style.display = 'none';
            if (fileInput)  fileInput.value  = '';
            if (hiddenInput) hiddenInput.value = ''; // backend else block will load from DB
        }
    }

    // ── Image preview ─────────────────────────────────────────────────────────
    function previews(id) {
        var file = event.target.files[0];
        if (file) {
            document.getElementById(id).src = URL.createObjectURL(file);
        }
    }

    // ── Main group video preview ──────────────────────────────────────────────
    function editVideoPreviews(index) {
        var fileInput = document.getElementById('blogImage_' + index);
        var file = fileInput.files[0];
        if (!file) return;

        var video  = document.getElementById('editMyVideo_' + index);
        var source = document.getElementById('blogvideo_' + index);
        var objectUrl = URL.createObjectURL(file);

        source.src = objectUrl;
        video.load();
        video.style.display = 'block';
        video.controls = true;
        video.onended = function () { URL.revokeObjectURL(objectUrl); };
    }

    // ── SubGroup video preview ────────────────────────────────────────────────
    function editSubVideoPreviews(subGroupIndex, flagInd) {
        var fileInput = document.getElementById('subBlogImage_' + subGroupIndex + '_' + flagInd);
        var file = fileInput.files[0];
        if (!file) return;

        var video  = document.getElementById('subEditMyVideo_' + subGroupIndex + '_' + flagInd);
        var source = document.getElementById('subBlogvideo_' + subGroupIndex + '_' + flagInd);
        var objectUrl = URL.createObjectURL(file);

        source.src = objectUrl;
        video.load();
        video.controls = true;
        video.onended = function () { URL.revokeObjectURL(objectUrl); };
    }

</script>

<script>
    var spinner = $('.loader');

    jQuery(function () {
        jQuery('#edit-form').submit(function (e) {
            e.preventDefault();
            spinner.show();
            var formData = new FormData(this);

            // ── Append main-group video files (file inputs have no name attr) ──
            document.querySelectorAll('[id^="blogImage_"]').forEach(function (fileInput) {
                if (fileInput.files.length > 0) {
                    var fieldName = fileInput.getAttribute('data-field-name');
                    if (fieldName) {
                        formData.set(fieldName, fileInput.files[0]);
                    }
                }
            });

            // ── Append subgroup video files (file inputs have no name attr) ───
            document.querySelectorAll('[id^="subBlogImage_"]').forEach(function (fileInput) {
                if (fileInput.files.length > 0) {
                    var fieldName = fileInput.getAttribute('data-field-name');
                    if (fieldName) {
                        formData.set(fieldName, fileInput.files[0]);
                    }
                }
            });

            jQuery.ajax({
                type: 'POST',
                url: "{{ route('editSystemFlag') }}",
                data: formData,
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function (data) {
                    spinner.hide();
                    if (jQuery.isEmptyObject(data.error)) {
                        location.reload();
                    }
                },
                error: function (xhr) {
                    spinner.hide();
                    var msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'An unknown error occurred.';
                    alert('Error: ' + msg);
                }
            });
        });
    });

    $(window).on('load', function () { $('.loader').hide(); });

    function validateJavascript(event) {
        var regex = new RegExp("^[<>]");
        var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
        if (regex.test(key)) { event.preventDefault(); return false; }
    }

    // ── Bucket radio ──────────────────────────────────────────────────────────
    $(document).on('change', '.bucket_radio', function () { changeBucketBlock($(this).val()); });

    function changeBucketBlock(val) {
        if (val == 'aws_bucket') {
            $('.aws_bucket').show(); $('.local').hide(); $('.google_bucket').hide(); $('.digital_ocean').hide();
        } else if (val == 'digital_ocean') {
            $('.aws_bucket').hide(); $('.local').hide(); $('.google_bucket').hide(); $('.digital_ocean').show();
        } else if (val == 'google_bucket') {
            $('.aws_bucket').hide(); $('.local').hide(); $('.digital_ocean').hide(); $('.google_bucket').show();
        } else if (val == 'local') {
            $('.aws_bucket').hide(); $('.local').show(); $('.digital_ocean').hide(); $('.google_bucket').hide();
        }
    }

    $(document).ready(function () { changeBucketBlock($('.bucket_radio:checked').val()); });

    var select_bucket = $('.select_bucket');
    $("#ThirdPartyPackage .agora")[1].after(select_bucket[0], select_bucket[1]);

    $(document).on('change', '[name="group[0][systemFlag][13][value]"]', function () {
        if ($(this).val() == '0')
            $(this).closest('div').parent('div').next('div').hide();
        else
            $(this).closest('div').parent('div').next('div').show();
    });
    $('[name="group[0][systemFlag][13][value]"]').change();
</script>
@endsection
