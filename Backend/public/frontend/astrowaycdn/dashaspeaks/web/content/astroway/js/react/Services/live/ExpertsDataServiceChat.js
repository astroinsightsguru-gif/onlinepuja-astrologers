class ExpertsDataServiceChat {
    constructor() {
        this.itemService = new ExpertService();
        this.state = {
            selectedItem: null,
            error: null,
            isLoaded: false,
            statusCode: 0,
            categoryId: 0,
            scountryiso: this.itemService.GetCountryCode(),
            data: [],
            SearchExpert: "",
            paging: null,
            showmoreprogresscount: 0,
            totalPages: 0,
            showMore: false,
        }
    }

    getFilterCategoryId() {
        let defaultDeeplinkCatId = getParameterByName("catid", window.location.href);
        let defaultCatId = getParameterByName("cid", window.location.href);
        let SessionCatId = sessionStorage.getItem("CategoryId");
        let filterCatId = $('#psychicCategories').val();
        if ((typeof filterCatId === 'string' || typeof filterCatId === 'number') && parseInt(filterCatId) > 0) {
            defaultCatId = parseInt(filterCatId);
        }
        else if ((typeof defaultDeeplinkCatId === 'string' || typeof defaultDeeplinkCatId === 'number') && parseInt(defaultDeeplinkCatId) > 0) {
            defaultCatId = parseInt(defaultDeeplinkCatId);
        }
        else if ((typeof defaultCatId === 'string' || typeof defaultCatId === 'number') && parseInt(defaultCatId) > 0) {
            defaultCatId = parseInt(defaultCatId);
        }
        else if ((typeof SessionCatId === 'string' || typeof SessionCatId === 'number') && parseInt(SessionCatId) > 0) {
            defaultCatId = parseInt(SessionCatId);
            sessionStorage.removeItem("CategoryId");
        }
        else if ((typeof g_CategoryId === 'string' || typeof g_CategoryId === 'number') && parseInt(g_CategoryId) > 0) {
            defaultCatId = parseInt(g_CategoryId);
        }
        if (defaultCatId > 0) {
            g_FilterCategoryId = defaultCatId;
        }
        return g_FilterCategoryId;
    }

    populateCategories() {
        if (document.getElementById("psychicCategories")) {
            var select = document.getElementById("psychicCategories");

            g_FilterCategoryId = this.getFilterCategoryId();

            for (var i = 0; i < jorderedCategories.length; i++) {
                var opt = jorderedCategories[i];
                var el = document.createElement("option");
                if (i == 0) {
                    el.textContent = "All";
                    el.value = 0;
                    select.appendChild(el);
                }
                el.textContent = htmlDecode(opt["nm"]);
                el.value = opt["id"];
                if (parseInt(el.value) == g_FilterCategoryId && g_FilterCategoryId != 0) {
                    el.selected = true;
                }
                select.appendChild(el);
            }
        }
    }

    getPrioritySortingList(dataitems) {
        //debugger;
        dataitems = dataitems.sort((firstItem, secondItem) => secondItem.priority - firstItem.priority);
        return dataitems;
    }

    getShuffledFreeRankList(dataitems) {
        //debugger;
        dataitems = dataitems.sort((firstItem, secondItem) => firstItem.FreeRanking - secondItem.FreeRanking);
        return dataitems;
    }

    getShuffledRankList(dataitems) {
        //debugger;
        dataitems = dataitems.sort((firstItem, secondItem) => firstItem.Ranking - secondItem.Ranking);
        return dataitems;
    }

    checkIfInRange(x, min, max) {
        if (x == min && max==0 && x==0) {
            return false;
        }
        return ((x - min) * (x - max) <= 0);
    }

    GetGroupData(dataitems, GroupObject, showGlobalOffer) {
        //console.log("dataitems ",dataitems);
        let filteredGroupData = {};
        let localfilterdata = {};
        let ApplicableOffer = {
            NONE:0,
            FREE: 1,
            LOW_PRICE: 2
        };
        let MyCurrentApplicableOffer = ApplicableOffer.NONE;

        if (GroupObject.hasOwnProperty("tot") && GroupObject.tot <= 0) {
            return filteredGroupData;
        }


        if (checkIfOfferAvailable(eEligibleArea.CHAT)) {
            let ModuleOfferType = parseInt(_CurrentOfferObj.OfferType);
            let ModuleOfferId = parseInt(_CurrentOfferObj.OfferId);
            if (ModuleOfferType == 10 && ModuleOfferId > 0) {
                MyCurrentApplicableOffer = ApplicableOffer.LOW_PRICE;
            } else if ((ModuleOfferType == 5 || ModuleOfferType == 6 || ModuleOfferType == 8 || ModuleOfferType == 9) && ModuleOfferId > 0) {
                MyCurrentApplicableOffer = ApplicableOffer.FREE;
            }
        }

        filteredGroupData = dataitems;


        ////MARK: 1 Filter Low Price and fme
        let hlpFlag = GroupObject.hasOwnProperty('hlp') ? GroupObject.hlp : -1;
        let fmeFlag = GroupObject.hasOwnProperty('fme') ? GroupObject.fme : -1;
        let showExpertinFree = (GroupObject.hasOwnProperty('sfe') && GroupObject.sfe > 0);
        if (fmeFlag == 1 && showExpertinFree == 1 && MyCurrentApplicableOffer == ApplicableOffer.FREE) {
            hlpFlag = -1;
        } else if (hlpFlag == 1 && MyCurrentApplicableOffer == ApplicableOffer.LOW_PRICE) {
            fmeFlag = -1;
        }
        if (hlpFlag != -1) {
            //means 1 or 0
            filteredGroupData = filteredGroupData.filter(e => { return e.IsLowPrice == hlpFlag });
        }

        if (filteredGroupData != null) {
            if (fmeFlag != -1) {
                let verifyFreeMinExpertExhausted = false;
                if (MyCurrentApplicableOffer == ApplicableOffer.LOW_PRICE && hlpFlag == 1) {
                    verifyFreeMinExpertExhausted = true;
                }
                //means fme= 1/0
                filteredGroupData = filteredGroupData.filter(e => { return (verifyFreeMinExpertExhausted ? (e.freemineligible == -1 || e.freemineligible == 1) : e.freemineligible == fmeFlag) });

                
            }

            if (filteredGroupData != null) {
                if (MyCurrentApplicableOffer == ApplicableOffer.LOW_PRICE) {
                    if (_customerDealsArray.length > 0) {
                        filteredGroupData = filteredGroupData.filter(function (item) {
                            return _customerDealsArray.filter(function (item2) {
                                return item.PsychicId == item2.ExpertId;
                            }).length == 0;
                        });
                    }
                }
                else if (MyCurrentApplicableOffer == ApplicableOffer.FREE) {
                    filteredGroupData = filteredGroupData.filter(e => { return e.freechatminute > 0 });
                }
            }
            
        }



        if (filteredGroupData != null) {
         
            //MARK: 2 Filter InHouseExpert
            if (GroupObject.hasOwnProperty('inh')) {
                let inhFlag = GroupObject.hasOwnProperty('inh') ? GroupObject.inh : -1;
                if (inhFlag != -1) {
                    filteredGroupData = filteredGroupData.filter(e => { return e.InHouse == inhFlag });
                }
                
            }

           
            //MARK: 3 Filter topPerformer
            if (GroupObject.hasOwnProperty('top') && GroupObject.top && GroupObject.top > 0) {
                filteredGroupData = filteredGroupData.filter(e => { return e.groupno == 2 });
            }

            //MARK: 3.1 Filter hasDeal
            if (GroupObject.hasOwnProperty('hsd')) {
                let hsdFlag = GroupObject.hasOwnProperty('hsd') ? GroupObject.hsd : -1;
                if (hsdFlag != -1) {
                    filteredGroupData = filteredGroupData.filter(e => { return e.HasChatDeal == hsdFlag });
                }

            }

            //MARK: 4 Filter minPrice
            if (GroupObject.hasOwnProperty('mip') && GroupObject.mip && GroupObject.mip > 0) {
                filteredGroupData = filteredGroupData.filter(e => { return e.ChargeRate >= GroupObject.mip });
            }
            if (GroupObject.hasOwnProperty('mxp') && GroupObject.mxp && GroupObject.mxp > 0) {
                filteredGroupData = filteredGroupData.filter(e => { return e.ChargeRate <= GroupObject.mxp });
            }

            //MARK: 6 Filter Badge
            //MARK: 3.1 Filter hasDeal
            if (GroupObject.hasOwnProperty('badge')) {
                let badgeFlag = GroupObject.hasOwnProperty('badge') ? GroupObject.badge : -1;
                if (badgeFlag != -1) {
                    filteredGroupData = filteredGroupData.filter(e => { return e.badge == badgeFlag });
                }
            }


            //MARK: 7 Filter Priority
            if (GroupObject.hasOwnProperty('pri') && GroupObject.pri && GroupObject.pri > 0) {
                filteredGroupData = this.getPrioritySortingList(filteredGroupData);
            }

            if (fmeFlag == 1 && showExpertinFree == 1 && MyCurrentApplicableOffer == ApplicableOffer.FREE) {
                //MARK: 8 Filter Priority
                if (GroupObject.hasOwnProperty('frnk') && GroupObject.frnk && GroupObject.frnk > 0) {
                    filteredGroupData = this.getShuffledFreeRankList(filteredGroupData);
                }
            }
            else {
                //MARK: 8 Filter Priority
                if (GroupObject.hasOwnProperty('rnk') && GroupObject.rnk && GroupObject.rnk > 0) {
                    filteredGroupData = this.getShuffledRankList(filteredGroupData);
                }
            }

            //MARK: 9 sorting

            filteredGroupData = this.checkIfExpertTakenRecentSession(filteredGroupData);

            if (filteredGroupData != null) {
                //MARK: 10 totalBatchCount
                let totFlag = GroupObject.hasOwnProperty('tot') ? GroupObject.tot : -1;

                if (totFlag > 0) {
                    _filterListExpectedCount += GroupObject.tot;
                    //find total out of it (with/without offer)
                    if (filteredGroupData.length > GroupObject.tot) {
                        filteredGroupData = filteredGroupData.slice(0, GroupObject.tot);
                    }

                    if (MyCurrentApplicableOffer == ApplicableOffer.LOW_PRICE && hlpFlag==1) {
                        //mark low price expert and get total out of it
                        localfilterdata = filteredGroupData.map((obj) => { (obj.IsLowPrice == 1) ? obj.showinfree = 1 : obj.showinfree = 0; return obj; });
                        filteredGroupData = localfilterdata;
                    }
                    else if (MyCurrentApplicableOffer == ApplicableOffer.FREE && showExpertinFree) {
                        //mark free expert and get total out of it
                        //counter used to show AI if no astrologes available in free
                        _gFreeExpertCounter++;
                        localfilterdata = filteredGroupData.map((obj) => { (obj.freemineligible == 1 && obj.freechatminute > 0) ? obj.showinfree = 1 : obj.showinfree = 0; return obj; });
                        filteredGroupData = localfilterdata;
                    }

                    
                }
                
            }

            ////MARK: 10 totalBatchCount
            //if (GroupObject.hasOwnProperty('tot') && GroupObject.tot && GroupObject.tot > 0 && filteredGroupData.length > 0) {
            //    if (filteredGroupData.length > GroupObject.tot) {
            //        filteredGroupData = filteredGroupData.slice(0, GroupObject.tot);
            //    }
            //}

            
        }

        //traceLog("group data", GroupObject);
        //traceLog("group wise data", filteredGroupData);
        return filteredGroupData;
    }

    randomIntFromInterval(min, max) { // min and max included 
        return Math.floor(Math.random() * (max - min + 1) + min)
    }

    PerformInjectOnGroupList(onlineastrologers, offbusyastrologers, expertcount, minRange, maxRange) {
        if (offbusyastrologers.length > 0) {
            for (var range = 0; range < expertcount; range++) {
                let randomnumber = this.randomIntFromInterval(minRange, maxRange);
                //traceLog("random number", randomnumber);
                let splicednumber = offbusyastrologers.splice(0, 1);
                onlineastrologers.splice(randomnumber, 0, splicednumber[0]);
            }
        }
        return [onlineastrologers, offbusyastrologers];
    }

    calculateMinMaxRange(groupRange) {
        let groupsortarr = [];
        if (groupRange.indexOf("-") > 0) {
            groupsortarr = groupRange.split("-");
        } else {
            groupsortarr[0] = groupRange;
        }
        return groupsortarr;
    }

    performExpertInjection(groupDataToPush, groupDataFromPull, settingsjson) {
    let expertsListAfterInjectioin = [];

    if ((groupDataToPush != null && groupDataToPush.length > 0) && (groupDataFromPull != null && groupDataFromPull.length > 0)) {
        //expertsListAfterInjectioin= groupDataToPush;
        let _localBsyAstrologers = groupDataFromPull.filter(e => { return e.onlineStatus == 2 });
        let _localOfflineAstrologers = groupDataFromPull.filter(e => { return e.onlineStatus == 3 });

        if (settingsjson.settings.hasOwnProperty("inject")) {
            if (settingsjson.settings.inject.hasOwnProperty("group")) {
                if (settingsjson.settings.inject.group != null && typeof settingsjson.settings.inject.group === "object" && settingsjson.settings.inject.group !== "undefined") {
                    let minRange = 0
                    let maxRange = 0

                    if (settingsjson.settings.inject.group.hasOwnProperty("rng") && settingsjson.settings.inject.group.rng != "") {
                        let groupsortarr = this.calculateMinMaxRange(settingsjson.settings.inject.group.rng);
                        if (groupsortarr.length > 1) {
                            minRange = parseInt(groupsortarr[0]);
                            maxRange = parseInt(groupsortarr[1]);
                            if (groupDataToPush.length <= maxRange) {
                                maxRange = groupDataToPush.length;
                            }
                        } else {
                            minRange = maxRange = parseInt(groupsortarr[0]);
                        }

                        let expertsListToInjectForGroup = [];
                        if (maxRange <= groupDataToPush.length) {
                            expertsListToInjectForGroup = groupDataToPush.splice(0, maxRange);

                            let expertOfflineCount = 0;
                            let expertBusyCount = 0;
                            /*inject offline astrologers on group*/
                            if (settingsjson.settings.inject.group.hasOwnProperty("off"))
                            {
                                if (settingsjson.settings.inject.group.off != "" && parseInt(settingsjson.settings.inject.group.off) > 0) {
                                    expertOfflineCount = parseInt(settingsjson.settings.inject.group.off);
                                    if (expertOfflineCount > _localOfflineAstrologers.length) {
                                        expertOfflineCount = _localOfflineAstrologers.length;
                                    }

                                    let _listafterOfflineInjection = this.PerformInjectOnGroupList(expertsListToInjectForGroup, _localOfflineAstrologers, expertOfflineCount, minRange, maxRange);
                                    expertsListToInjectForGroup = _listafterOfflineInjection[0];
                                    _localOfflineAstrologers = _listafterOfflineInjection[1];
                                }
                            }
                            
                            /*inject busy astrologers on group*/
                            if (settingsjson.settings.inject.group.hasOwnProperty("bsy")) {
                                
                                if (settingsjson.settings.inject.group.bsy != "" && parseInt(settingsjson.settings.inject.group.bsy) > 0) {
                                    expertBusyCount = parseInt(settingsjson.settings.inject.group.bsy);
                                    if (expertBusyCount > _localBsyAstrologers.length) {
                                        expertBusyCount = _localBsyAstrologers.length;
                                    }

                                    let _listafterBsyInjection = this.PerformInjectOnGroupList(expertsListToInjectForGroup, _localBsyAstrologers, expertBusyCount, minRange, maxRange);

                                    expertsListToInjectForGroup = _listafterBsyInjection[0];
                                    _localBsyAstrologers = _listafterBsyInjection[1];
                                }
                            }
                        }
                        Array.prototype.push.apply(expertsListAfterInjectioin, expertsListToInjectForGroup);
                    } else {
                        Array.prototype.push.apply(expertsListAfterInjectioin, groupDataToPush);
                    }
                }
            }
            

            /* injection on rest astrologers*/
            if (settingsjson.settings.inject.hasOwnProperty("rest")) {
                if (settingsjson.settings.inject.rest != null && typeof settingsjson.settings.inject.rest === "object" && settingsjson.settings.inject.rest !== "undefined") {
                    let expertOfflineCount = 0;
                    let expertBusyCount = 0;
                    let _restminRange = 0;
                    let _restmaxRange = groupDataToPush.length;
                    /*inject offline astrologers on rest*/
                    if (settingsjson.settings.inject.rest.hasOwnProperty("off")) {
                        if (settingsjson.settings.inject.rest.off != "" && parseInt(settingsjson.settings.inject.rest.off) > 0) {
                            expertOfflineCount = parseInt(settingsjson.settings.inject.rest.off);
                            if (expertOfflineCount > _localOfflineAstrologers.length) {
                                expertOfflineCount = _localOfflineAstrologers.length;
                            }

                            let _listafterOfflineInjection = this.PerformInjectOnGroupList(groupDataToPush, _localOfflineAstrologers, expertOfflineCount, _restminRange, _restmaxRange);
                            groupDataToPush = _listafterOfflineInjection[0];
                            _localOfflineAstrologers = _listafterOfflineInjection[1];
                        }
                    }
                    /*inject busy astrologers on rest*/
                    if (settingsjson.settings.inject.rest.hasOwnProperty("bsy")) {
                        
                        if (settingsjson.settings.inject.rest.bsy != "" && parseInt(settingsjson.settings.inject.rest.bsy) > 0) {
                            expertBusyCount = parseInt(settingsjson.settings.inject.rest.bsy);
                            if (expertBusyCount > _localBsyAstrologers.length) {
                                expertBusyCount = _localBsyAstrologers.length;
                            }

                            let _listafterBsyInjection = this.PerformInjectOnGroupList(groupDataToPush, _localBsyAstrologers, expertBusyCount, _restminRange, _restmaxRange);

                            groupDataToPush = _listafterBsyInjection[0];
                            _localBsyAstrologers = _listafterBsyInjection[1];

                        }
                    }
                    Array.prototype.push.apply(expertsListAfterInjectioin, groupDataToPush);
                }
            }
            
        }


        

        Array.prototype.push.apply(expertsListAfterInjectioin, _localBsyAstrologers);
        Array.prototype.push.apply(expertsListAfterInjectioin, _localOfflineAstrologers);
    }
    return expertsListAfterInjectioin;
}

    expertsLastSessionListFilter(filteredData, ShuffledAstrologersList, rntmpricevariance, rntmpricethreshold, dExpertChargePerMin) {
        let expertsListAfterFilter = [];
        let combinedExpertsList = [];
        let dupdatedChargePerMin = dExpertChargePerMin - rntmpricevariance;
        if (dupdatedChargePerMin > rntmpricethreshold) {
            dupdatedChargePerMin = rntmpricethreshold;
        }
        if (dExpertChargePerMin > 0) {
            let filteredExpertsData = filteredData.filter(e => { return (e.ChargeRate >= dupdatedChargePerMin) });
            if (filteredExpertsData != null && filteredExpertsData.length > 0) {
                if (filteredExpertsData.length < _filterListExpectedCount) {
                    let remainingListToGet = _filterListExpectedCount - filteredExpertsData.length;

                    let filteredExpertsDataShuffled = ShuffledAstrologersList.filter(e => { return (e.ChargeRate >= dupdatedChargePerMin) });
                    if (remainingListToGet > filteredExpertsDataShuffled.length) {
                        remainingListToGet = filteredExpertsDataShuffled.length;
                    }
                    Array.prototype.push.apply(filteredExpertsData, filteredExpertsDataShuffled.slice(0, remainingListToGet));

                    // Array.prototype.push.apply(combinedExpertsList, filteredExpertsDataShuffled);

                }
            } else {
                let filteredExpertsDataShuffled = ShuffledAstrologersList.filter(e => { return (e.ChargeRate >= dupdatedChargePerMin) });
                if (_filterListExpectedCount > filteredExpertsDataShuffled.length) {
                    _filterListExpectedCount = filteredExpertsDataShuffled.length;
                }
                Array.prototype.push.apply(filteredExpertsData, filteredExpertsDataShuffled.slice(0, _filterListExpectedCount));
            }
            

            Array.prototype.push.apply(combinedExpertsList, filteredData);
            Array.prototype.push.apply(combinedExpertsList, ShuffledAstrologersList);

            let remainingShuffledOnlineAstrologers = combinedExpertsList.filter(function (item) {
                return filteredExpertsData.filter(function (item2) {
                    return item.PsychicId == item2.PsychicId;
                }).length == 0;
            });

            Array.prototype.push.apply(expertsListAfterFilter, filteredExpertsData);
            Array.prototype.push.apply(expertsListAfterFilter, remainingShuffledOnlineAstrologers);
            return expertsListAfterFilter;
        }

        Array.prototype.push.apply(expertsListAfterFilter, filteredData);
        Array.prototype.push.apply(expertsListAfterFilter, ShuffledAstrologersList);
        return expertsListAfterFilter;
    }

    shufflegroupwiseData(shuffledOnlineAstrologers, groupname, settingsjson, showGlobalOffer) {
       // console.log("shuffledOnlineAstrologers", shuffledOnlineAstrologers);
        let modifygroupshuffle = false;
        let groupRangeArray = [];
        let shuffleManually = false;

        let groupArray = [];
        if (groupname == "groups") {
            groupArray = settingsjson["groups"];

            let groupShuffleKey = "groupsort"; // change it to gshfl when required

            if (settingsjson.settings.hasOwnProperty(groupShuffleKey)) {
                if (!(settingsjson.settings[groupShuffleKey] instanceof Array) && typeof settingsjson.settings[groupShuffleKey] === "string") {
                    groupRangeArray.push(settingsjson.settings[groupShuffleKey]);
                } else {
                    groupRangeArray = settingsjson.settings[groupShuffleKey];

                }
            }



        } else {
            groupArray = settingsjson["timewise"]["tgroups"];

            let groupShuffleKey = "tgshfl"; // change it to gshfl when required

            if (settingsjson["timewise"].hasOwnProperty(groupShuffleKey)) {
                if (!(settingsjson["timewise"][groupShuffleKey] instanceof Array) && typeof settingsjson["timewise"][groupShuffleKey] === "string") {
                    groupRangeArray.push(settingsjson["timewise"][groupShuffleKey]);
                } else {
                    groupRangeArray = settingsjson["timewise"][groupShuffleKey];
                }
            }

            shuffleManually = true;
        }


        let GroupsToShuffle = [];
        if (groupRangeArray instanceof Array && groupRangeArray.length > 0) {
            let shuffleCurrentRange = false;

            let minCurrentRange = 0;
            let maxCurrentRange = 0;
            for (let i = 0; i < groupRangeArray.length; i++) {
                let groupsortarr = this.calculateMinMaxRange(groupRangeArray[i]);
                if (groupsortarr.length > 1) {
                    minCurrentRange = parseInt(groupsortarr[0]);
                    maxCurrentRange = parseInt(groupsortarr[1]);
                } else {
                    minCurrentRange = maxCurrentRange = parseInt(groupsortarr[0]);
                }
                if (minCurrentRange >= 0 && maxCurrentRange > minCurrentRange) {
                    shuffleCurrentRange = true;
                }
                GroupsToShuffle.push({ "min": minCurrentRange, "max": maxCurrentRange, "shuffle": shuffleCurrentRange });
            }


        }

        if (groupArray instanceof Array && groupArray.length > 0) {
            //logic here
            //let GroupsToShuffle = [{ "min": 1, "max": 2 }, { "min": 4, "max": 5 }];

            let _tempShuffleGroup = [];
            let ProcessedGroup = [];
            let GroupInProcessToShuffleIdx = 0;
            //logic here
            let GroupInProcessToShuffle = null;
            if (GroupsToShuffle != null && GroupsToShuffle instanceof Array && GroupsToShuffle.length > 0 && GroupsToShuffle[GroupInProcessToShuffleIdx].hasOwnProperty("min")) {
                GroupInProcessToShuffle = GroupsToShuffle[GroupInProcessToShuffleIdx];
            }

            let _ElementToShuffleInGroup = false;


            for (let i = 0; i < groupArray.length; i++) {

                //#region Whether to skik the group or not
                if (groupArray[i].hasOwnProperty("src") && groupArray[i]["src"] instanceof Array && groupArray[i]["src"].length > 0) {
                    if (groupArray[i]["src"].indexOf(p_utm_source) < 0) {

                        continue;
                    }
                }

                if (groupArray[i].hasOwnProperty("sfe") && groupArray[i]["sfe"] == 1 && !showGlobalOffer) {
                    continue;
                }

                let currentSystemHours = parseInt((new Date()).getHours());
                let currentSystemhourMatched = false;
                if (groupArray[i].hasOwnProperty("tmr") && groupArray[i]["tmr"] instanceof Array && groupArray[i]["tmr"].length > 0) {

                        let minCurrentTimeRange = 0;
                        let maxCurrentTimeRange = 0;
                        for (let groupTimeRange = 0; groupTimeRange < groupArray[i]["tmr"].length; groupTimeRange++) {
                            let CurrentTimearr = this.calculateMinMaxRange(groupArray[i]["tmr"][groupTimeRange]);
                            if (CurrentTimearr.length > 1) {
                                minCurrentTimeRange = parseInt(CurrentTimearr[0]);
                                maxCurrentTimeRange = parseInt(CurrentTimearr[1]);
                            } else {
                                minCurrentTimeRange = maxCurrentTimeRange = parseInt(CurrentTimearr[0]);
                            }

                            if (currentSystemHours >= minCurrentTimeRange && currentSystemHours <= maxCurrentTimeRange && !currentSystemhourMatched) {
                                currentSystemhourMatched = true;
                            }

                        }
                        if (!currentSystemhourMatched) {

                            continue;
                        }
                }
                //end region

                if (shuffledOnlineAstrologers.length > 0) {

                    let _groupResult = this.GetGroupData(shuffledOnlineAstrologers, groupArray[i], showGlobalOffer);

                    if (GroupInProcessToShuffle != null && typeof GroupInProcessToShuffle === 'object' && GroupInProcessToShuffle.hasOwnProperty('min') && i >= GroupInProcessToShuffle.min && i <= GroupInProcessToShuffle.max) {
                        _ElementToShuffleInGroup = true;
                        Array.prototype.push.apply(_tempShuffleGroup, _groupResult);
                    }
                    else {
                        if (_ElementToShuffleInGroup) {
                            //shuffle "_tempShuffleGroup" 
                            let _tmpGroupDataShuffled = _tempShuffleGroup.sort(() => Math.random() - 0.5);

                            Array.prototype.push.apply(ProcessedGroup, _tmpGroupDataShuffled);
                            Array.prototype.push.apply(ProcessedGroup, _groupResult);

                            //reset shuffled logics
                            _ElementToShuffleInGroup = false;
                            _tempShuffleGroup = [];
                            //set GroupInProcessToShuffle for range
                            //ex: GroupInProcessToShuffle = { "min": 5, "max": 6 };
                            //logic here
                            if (GroupsToShuffle != null && GroupsToShuffle instanceof Array && GroupsToShuffle.length > GroupInProcessToShuffleIdx + 1 && GroupsToShuffle[GroupInProcessToShuffleIdx + 1].hasOwnProperty("min")) {
                                GroupInProcessToShuffle = GroupsToShuffle[++GroupInProcessToShuffleIdx];
                            }
                        }
                        else {
                            //add, do not shuffle
                            Array.prototype.push.apply(ProcessedGroup, _groupResult);
                        }
                    }

                    shuffledOnlineAstrologers = shuffledOnlineAstrologers.filter(function (item) {
                        return _groupResult.filter(function (item2) {
                            return item.PsychicId == item2.PsychicId;
                        }).length == 0;
                    });

                }
            }

            if (_ElementToShuffleInGroup) {
                //shuffle "_tempShuffleGroup" 
                let _tmpGroupDataShuffled = _tempShuffleGroup.sort(() => Math.random() - 0.5);

                Array.prototype.push.apply(ProcessedGroup, _tmpGroupDataShuffled);

                //reset shuffled logics
                _ElementToShuffleInGroup = false;
                _tempShuffleGroup = [];
                GroupInProcessToShuffle = null;
            }

            if (shuffleManually) {
                ProcessedGroup = ProcessedGroup.sort(() => Math.random() - 0.5);
            }

            //Array.prototype.push.apply(ProcessedGroup, shuffledOnlineAstrologers);
            //shuffledOnlineAstrologers = ProcessedGroup;
            return [ProcessedGroup, shuffledOnlineAstrologers];
           // return ProcessedGroup;
        }

        //return shuffledOnlineAstrologers;
    }

    getFinalAstrologersList(dataitems, expertconfig) {
        if (expertconfig.hasOwnProperty("tuck_free_exp") && (typeof expertconfig.tuck_free_exp === 'string' || typeof expertconfig.tuck_free_exp === 'number') && parseInt(expertconfig.tuck_free_exp) > 0) {
            let finalAstrologersListAfterFreeExperts = [];
            let filteredFreeExpertsData = dataitems.filter(e => { return (e.showinfree == 1 && e.onlineStatus==1) });

                let remainingAstrologersList = dataitems.filter(function (item) {
                    return filteredFreeExpertsData.filter(function (item2) {
                        return item.PsychicId == item2.PsychicId;
                    }).length == 0;
                });
                Array.prototype.push.apply(finalAstrologersListAfterFreeExperts, filteredFreeExpertsData);
                Array.prototype.push.apply(finalAstrologersListAfterFreeExperts, remainingAstrologersList);
                return finalAstrologersListAfterFreeExperts;
            }
            return dataitems;
    }



    getFilteredAstrologers(dataitems, showFree, AIapplyFilter = false) {
        let _RecommendedListFilter = false;

        if (typeof AIapplyFilter === 'boolean' && AIapplyFilter === true) {
            _RecommendedListFilter = true;
        }
        _gFreeExpertCounter = 0;
        var settingsjson = {};
        if (jExprtGrpPolicyjObject != null && typeof jExprtGrpPolicyjObject === "object" && jExprtGrpPolicyjObject.hasOwnProperty("settings")) {
           settingsjson = jExprtGrpPolicyjObject;
        }
        
        try {

            /*//separate experts from list
            => shuffle records
           online            
               => find random 10 and mark them as free astrologer
               => get new online experts - 10 free marked astroloer => "Group 1 Experts"
               => remaining online expert = 40  (rem_online)
               => rem_online
                   Find Group No 2 experts and list them as "Group 2 Experts"
   
               => Treat Left sliced records as "Group 2 Experts"
   
               now create new array as "shuffled astrologer list"
   
               shuffled_list.push(free);
               shuffled_list.push(top_paid);
               shuffled_list.push(remaining);
   
           offline*/
            let OldAstrologerItem = [];
            Array.prototype.push.apply(OldAstrologerItem, dataitems);
            //console.log("First Astrologers List", OldAstrologerItem);

            if (settingsjson != null && typeof settingsjson === "object" && settingsjson.hasOwnProperty("settings")) {
                let showGlobalOffer = false;
                
                if ((settingsjson.hasOwnProperty("groups") && settingsjson.groups.length > 0) || (settingsjson.hasOwnProperty("timewise") && settingsjson.timewise.hasOwnProperty("tgroups") && settingsjson.timewise.tgroups.length > 0) || settingsjson != null && typeof settingsjson === "object" && settingsjson.hasOwnProperty("rastro")) {
                    //shuffle records
                    let shuffledAstrologersList = OldAstrologerItem.sort(() => Math.random() - 0.5);
                    //shuffledAstrologersList = OldAstrologerItem;
                    //online astrologers from shuffled list
                    let shuffledOnlineAstrologers = shuffledAstrologersList.filter(e => {
                        return e.onlineStatus == 1
                    });

                    //console.log("before grouping Online Astrologers", shuffledOnlineAstrologers);
                    //Shuffled Offline Astrologer
                    let shuffledOfflineAstrologers = shuffledAstrologersList.filter(value => !shuffledOnlineAstrologers.includes(value));

                    if (settingsjson.settings.showfreeoffer > 0 && showFree) {
                        showGlobalOffer = true;
                    }


                    if (_RecommendedListFilter === true && (settingsjson.rastro != null && typeof settingsjson.rastro === "object" && settingsjson.rastro.hasOwnProperty("rgroups") && settingsjson.rastro.rgroups != null && typeof settingsjson.rastro.rgroups === "object" && settingsjson.rastro.rgroups.length > 0)) {


                        let recommendedAstrologersList = shuffledOnlineAstrologers;
                        let recommendedFilteredGroupData = [];
                        for (let i = 0; i < settingsjson.rastro.rgroups.length; i++) {
                            let shuffledGroupsData = this.GetGroupData(recommendedAstrologersList, settingsjson.rastro.rgroups[i], showGlobalOffer);
                            Array.prototype.push.apply(recommendedFilteredGroupData, shuffledGroupsData);
                            recommendedAstrologersList = recommendedAstrologersList.filter(function (item) {
                                return shuffledGroupsData.filter(function (item2) {
                                    return item.PsychicId == item2.PsychicId;
                                }).length == 0;
                            });
                        }

                        Array.prototype.push.apply(recommendedFilteredGroupData, recommendedAstrologersList);
                        //Array.prototype.push.apply(recommendedFilteredGroupData, shuffledOfflineAstrologers);

                        if (settingsjson.rastro.hasOwnProperty('tot') && settingsjson.rastro.tot && settingsjson.rastro.tot > 0 && recommendedFilteredGroupData.length > 0) {
                            if (recommendedFilteredGroupData.length > settingsjson.rastro.tot) {
                                recommendedFilteredGroupData = recommendedFilteredGroupData.slice(0, settingsjson.rastro.tot);
                            }
                        }

                        let updatedAstrologerListWithListenersEnabled = [];
                        if (recommendedFilteredGroupData != null && recommendedFilteredGroupData.length > 0) {
                            updatedAstrologerListWithListenersEnabled = recommendedFilteredGroupData.map((obj, index) =>

                                ((parseInt(G_ExpertListenerCounter) == 0 || index >= parseInt(G_ExpertListenerCounter))) ? obj : {
                                    ...obj, EnableExpertListListener: parseInt(G_IsExpertListenerEnabled) == 1 ? 1 : 0
                                }
                            );

                            return updatedAstrologerListWithListenersEnabled;
                        }

                        return OldAstrologerItem;

                    }
                    let finalgroupFilterData = [];
                    let groupwiseDataArray = this.shufflegroupwiseData(shuffledOnlineAstrologers, "groups", settingsjson, showGlobalOffer);
                    if (groupwiseDataArray != null && groupwiseDataArray.length > 0) {
                        if (PrevSessionAstrologers != null && PrevSessionAstrologers.length > 0 && settingsjson.settings.hasOwnProperty("blrntmpricefilter") && settingsjson.settings.blrntmpricefilter > 0 && !showGlobalOffer) {

                            /**expertsLastSessionListFilter */
                            let processedExpertsList = groupwiseDataArray[0];
                            let remainingShuffledOnlineExpertsList = groupwiseDataArray[1];
                            let recentExpertChargePerMin = PrevSessionAstrologers[0]['ChargePerMin'];

                            let rntmpricevariance = 0;
                            let rntmpricethreshold = 0;
                            if (settingsjson.settings.hasOwnProperty("rntmpricevariance")) {
                                if (settingsjson.settings.rntmpricevariance > 0) {
                                    rntmpricevariance = settingsjson.settings.rntmpricevariance;
                                }
                            }
                            if (settingsjson.settings.hasOwnProperty("rntmpricethreshold")) {
                                if (settingsjson.settings.rntmpricethreshold > 0) {
                                    rntmpricethreshold = settingsjson.settings.rntmpricethreshold;
                                }
                            }

                            let expertsListAfterNewFilter = this.expertsLastSessionListFilter(processedExpertsList, remainingShuffledOnlineExpertsList, rntmpricevariance, rntmpricethreshold, recentExpertChargePerMin);


                            Array.prototype.push.apply(finalgroupFilterData, expertsListAfterNewFilter);
                            //Array.prototype.push.apply(finalgroupFilterData, remainingShuffledOnlineExpertsList);
                        } else {
                            let processedExpertsList = groupwiseDataArray[0];
                            let remainingShuffledOnlineExpertsList = groupwiseDataArray[1];

                            Array.prototype.push.apply(finalgroupFilterData, processedExpertsList);
                            Array.prototype.push.apply(finalgroupFilterData, remainingShuffledOnlineExpertsList);
                        }
                    }

                   // let finalgroupFilterData = this.shufflegroupwiseData(shuffledOnlineAstrologers, "groups", settingsjson, showGlobalOffer);
                    //console.log(finalgroupFilterData);

                    if (settingsjson.hasOwnProperty("timewise") && settingsjson.timewise.hasOwnProperty("tgroups") && settingsjson.timewise.tgroups.length > 0) {
                         //finalgroupFilterData = this.shufflegroupwiseData(finalgroupFilterData, "timewise.tgroups", settingsjson, showGlobalOffer);
                        // console.log(finalgroupFilterData);

                        let timegroupwiseDataArray = this.shufflegroupwiseData(finalgroupFilterData, "timewise.tgroups", settingsjson, showGlobalOffer);
                        if (timegroupwiseDataArray != null && timegroupwiseDataArray.length > 0) {
                            Array.prototype.push.apply(finalgroupFilterData, timegroupwiseDataArray[0]);
                            Array.prototype.push.apply(finalgroupFilterData, timegroupwiseDataArray[1]);
                        }

                    }

                    //let finalgroupFilterData = this.shufflegroupwiseData(groupwiseShuffledData, "timewise.tgroups", settingsjson, showGlobalOffer);
                    //console.log(finalgroupFilterData);
                    
                    let totalExpertsAfterInjection = this.performExpertInjection(finalgroupFilterData, shuffledOfflineAstrologers, settingsjson);
                    let updatedAstrologerListWithListenersEnabled = [];
                    if (totalExpertsAfterInjection != null && totalExpertsAfterInjection.length > 0) {
                        updatedAstrologerListWithListenersEnabled = totalExpertsAfterInjection.map((obj, index) =>

                            ((parseInt(G_ExpertListenerCounter) == 0 || index >= parseInt(G_ExpertListenerCounter))) ? obj : {
                                ...obj, EnableExpertListListener: parseInt(G_IsExpertListenerEnabled) == 1 ? 1 : 0
                            }
                        );


                        updatedAstrologerListWithListenersEnabled = this.performPriorityInjection(updatedAstrologerListWithListenersEnabled, settingsjson);
                        updatedAstrologerListWithListenersEnabled = this.performEidsInjection(updatedAstrologerListWithListenersEnabled, settingsjson);

                        updatedAstrologerListWithListenersEnabled = this.getFinalAstrologersList(updatedAstrologerListWithListenersEnabled, settingsjson.settings);
                        return updatedAstrologerListWithListenersEnabled;
                    } else {

                        // Array.prototype.push.apply(finalgroupFilterData, shuffledOnlineAstrologers);
                        Array.prototype.push.apply(finalgroupFilterData, shuffledOfflineAstrologers);
                        //set EnableExpertListListener=1
                        updatedAstrologerListWithListenersEnabled = finalgroupFilterData.map((obj, index) =>

                            ((parseInt(G_ExpertListenerCounter) == 0 || index >= parseInt(G_ExpertListenerCounter))) ? obj : {
                                ...obj, EnableExpertListListener: parseInt(G_IsExpertListenerEnabled) == 1 ? 1 : 0
                            }
                        );
                        updatedAstrologerListWithListenersEnabled = this.performPriorityInjection(updatedAstrologerListWithListenersEnabled, settingsjson);
                        updatedAstrologerListWithListenersEnabled = this.performEidsInjection(updatedAstrologerListWithListenersEnabled, settingsjson);

                        updatedAstrologerListWithListenersEnabled = this.getFinalAstrologersList(updatedAstrologerListWithListenersEnabled, settingsjson.settings);
                        return updatedAstrologerListWithListenersEnabled;
                    }


                } else {
                    return OldAstrologerItem;
                }
            }

            return OldAstrologerItem;

        } catch (e) {
            return dataitems;
        }
    }

    getReviews(rating,reviewCount) {

        let totalreviewCount = reviewCount;
        if (reviewCount == 0) {
            return '<span class="font-12 weight500 m-0 p-0 colorblack noreview">' + getLanguageKeyString('EP_No_Review') + '</span>';
        } else {
            var reviewstring = '<span class="colorblack font-12 m-0 p-0 d-block">' + getLanguageKeyString('EP_Review') + ' : <span class="font-12 font-weight-bold m-0 p-0 color-brown">' + totalreviewCount + '</span></span >';
            reviewstring += '<i data-star="'+ rating+'"></i>';
            return reviewstring;
        }
        /*return '<div><p class="text-sm mb-0 text-muted">' + getLanguageKeyString('EP_Review') + '</p><span class="h5 font-weight-semi-bold d-md-block d-none font-normal" >' + reviewCount + '</span></div>';*/
    }

    RoundPriceInMultipleOf5(price, multipleof) {
        return Math.ceil(price);
        //return (Math.ceil(parseFloat(price) / multipleof) * multipleof);
    }

    async getExpertList(_showmore, searchText, filterEnum, isFav, catid,pageoptions=null,AIapplyFilter=false) {
        //debugger;
        let _RecommendedListFilter = false;
        let _SearchExpert = false;
        var nCatId = parseInt(catid);
        var isShowFavList = false;
        if (typeof isFav === 'boolean' && isFav === true) {
            isShowFavList = true;
        }
        else {
            if (typeof isMyExpertPage === 'boolean' && isMyExpertPage === true) {
                isShowFavList = true;
            }
        }
        if (typeof AIapplyFilter === 'boolean' && AIapplyFilter === true) {
            _RecommendedListFilter = true;
        }

        var ExpertSearchOptions = {
            isActive: true,
            page: 1,
            pageSize: 300,
            customerId: gloggedInUserid,
            categoryId: nCatId,
            lowestPrice: 0,
            highestPrice: 0,
            reviews: 0,
            rating: 0,
            orderBy: 1,
            countryiso: "",
            ShowTestPsychic: true,
            SearchText: "",
            FetchFavorites: isShowFavList,
            ExpertFeature: 1,
            culture: parseInt(g_CultureId)
        };

        if ((typeof searchText === 'string' && searchText != "") || (typeof this.state.SearchExpert === 'string' && this.state.SearchExpert != "")) {
            _SearchExpert = true;
            searchText = (searchText == "") ? this.state.SearchExpert : searchText;
        }
        else {
            _SearchExpert = false;
        }

        if (pageoptions != null) {
            this.state = {
                isLoaded: pageoptions.isLoaded,
                paging: pageoptions.paging,
            };
        }

        var progressCounter = 0;
        if (this.state.isLoaded) {
            if (this.state.paging == null) {
                this.state.paging = { page: 0, pageSize: 18, totalPages: 0 };
            } else {
                ExpertSearchOptions.page = this.state.paging.page == 0 ? 1 : this.state.paging.page;
            }
            if (_showmore) {
                ExpertSearchOptions.page++;
            }
            ExpertSearchOptions.pageSize = this.state.paging.pageSize;
            ExpertSearchOptions.categoryId = this.state.categoryId;
            ExpertSearchOptions.lowestPrice = 0.0;
            ExpertSearchOptions.highestPrice = 0.0;
            progressCounter = this.state.showmoreprogresscount;

            if (typeof filterEnum === 'string' && filterEnum != "") {
                ExpertSearchOptions.orderBy = filterEnum;
            }
        }
        var ExpertListService = {};
        if (_SearchExpert) {
            ExpertSearchOptions.SearchText = searchText;
        }
        if (typeof categoryId === 'number' || nCatId > 0) {
            ExpertSearchOptions.categoryId = (nCatId > 0) ? nCatId : categoryId;
        }

        ExpertListService = (await this.itemService.GetExpertAsync(ExpertSearchOptions));
        if (ExpertListService) {
            progressCounter++;
            if (ExpertListService.StatusCode == 200) {

                var npaging = (this.state.paging != null) ? this.state.paging : null;
                var olddataitems = (this.state.data != null) ? this.state.data : [];
                var dataitems = [];// olddataitems;
                var result = ExpertListService.Result.PsychicCardViewModel;
                var serviceConfig = ExpertListService.Result.Config;
                var _ResultFound = false;
                if (typeof result === 'object' && result.hasOwnProperty('Items') && result.Items instanceof Array && result.Items.length > 0) {
                    npaging = { page: result.Page, pageSize: result.PageSize, totalPages: result.TotalPages };
                    dataitems = result.Items;
                    _ResultFound = true;
                }


                if (serviceConfig.purchaseddeals!=null && serviceConfig.purchaseddeals.length > 0) {
                    _customerDealsArray = serviceConfig.purchaseddeals;
                }


                if (serviceConfig.CurrentTime > 0) {
                    let _currentUTCTimeStampForList = serviceConfig.CurrentTime;
                    let ChatBeginTimeStampServerUTC = parseInt(_currentUTCTimeStampForList);
                    let LocalTimeStampUTC = localTimeStampForList();
                    ChatBeginTimeStampOffsetForList = ChatBeginTimeStampServerUTC - LocalTimeStampUTC;
                }

                if (_SearchExpert) {
                    _isAITiletoShow = false;
                    olddataitems = this.getFilteredAstrologers(dataitems, false, _RecommendedListFilter);
                    //olddataitems = dataitems;                    
                }
                else {
                    Array.prototype.push.apply(olddataitems, dataitems);
                    //do not shuffle/ do not show free - for search filter
                    olddataitems = this.getFilteredAstrologers(olddataitems, checkIfOfferAvailable(eEligibleArea.CHAT), _RecommendedListFilter);
                }



                this.state = {
                    isLoaded: _ResultFound,
                    data: olddataitems,
                    paging: npaging,
                    statusCode: ExpertListService.StatusCode,
                    SearchExpert: searchText,
                    showmoreprogresscount: progressCounter,
                    config: serviceConfig
                };
                $('#expert-favorite-list').removeClass("bg-white");
                return this.state;
            }
            else {

                this.state = {
                    isLoaded: false,
                    data: null,
                    paging: this.state.paging,
                    statusCode: ExpertListService.StatusCode,
                    showmoreprogresscount: progressCounter,
                    showMore: false,
                    config: null
                };
                this.state.showMore = false;
                return this.state;
            }
        }
        return this.state;

    }

    getFreeMinuteText(isrechargeofferavailable, FreeMinuteForOffer) {
        let freeminutetexthtml = '';
        if (isrechargeofferavailable) {
            freeminutetexthtml = '<span class="font-11 d-block font-weight-semi-bold text-center text-success" style="margin-top:2px;">' + getLanguageKeyString("GLB_FiveMinFree").format(FreeMinuteForOffer) + '</span>';
        }
        return freeminutetexthtml;
    } 
    
    getMustTryBadge(badgeid) {
        var expertBadges = {};
        var expertBadge = null;
        if (jExpertBadges instanceof Array && jExpertBadges.length > 0) {
            expertBadges = jExpertBadges.filter(e => {
                return e.Id == badgeid
            });;
        }
        if (expertBadges instanceof Array && expertBadges.length>0) {
            expertBadge = expertBadges[0];
        }

        if (expertBadge != null && typeof expertBadge === 'object') {
            return '<span class="d-none must-try-badge font-10 position-absolute font-weight-semi text-center align-items-center justify-content-center text-white">' + expertBadge.Badge
                + '</span>';
        }
        else {
            return '';
        }
    }

    noRecordFound() {
        return '<div class="col-md-12 pt-5 pb-5 text-center bg-white" ><img src="https://cdn.anytimeastro.com/dashaspeaks/web/content/images/no-favorites.svg" class = "mt-4" alt = "Content Not Found" /><h4 class="weight500 font20 colorblack mt-4 mb-2">' + getLanguageKeyString("CWA_No_Expert_Found") + '</h4></div>';
    }

    showMoreButton(paging) {
        return '<div id="ShowMoreDiv" data-page="' + paging.page + '" data-pageSize="' + paging.pageSize + '" data-totalPages="' + paging.totalPages + '" class="col-sm-12 mt-3 text-center m-auto pt-4 pb-4"><a class="btn btn-lg bg-white rounded text-dark font-weight-bold bordershowmore py-md-3 py-2 px-md-5 px-4 mb-3" role = "button" id = "ShowMore" onClick="onShowMoreExpertList()">Show More<span></span ><span></span><span></span></a></div >';
    }

    ExpertActionBtn(mdata, config) {
        let expert = mdata;
        //  console.log("function data", expert);
      
        let CallChatBtnShow = config.CallChatBtnShow;
        let noExpertPic = cDNBaseUrl + "/web/content/images/no-expert-pic.png";  /*"https://cdn.hipsychic.com/hipsychic/web/content/images/no-expert-pic.png";*/
        let expertPic = (expert.Picture) ? cDNBaseUrl + "/psychics/" + expert.Picture : noExpertPic;
        //let categoryName = $('#cat_' + expert.CategoryId).attr('cat-name');
        let categoryName = config.categoryName;

        let url = gWebsitePrefix + "experts/emails/" + expert.PsychicId + "/new/";
        let expertCharge = config.expertCharge;
        let expertCallCharge = config.expertCallCharge;
        let expertUSDCharge = config.expertUSDCharge;
        let expertUSDCallCharge = config.expertUSDCallCharge;
        let expertChargeToShow = config.expertChargeToShow;
        let expertChargeType = config.expertChargeType;
        let expertDetailsUrl = gWebsitePrefix + "experts/" + expert.Slug + "/";
        let expertLocaleUrl = config.expertlocaleurl;
        let currency = config.UserCurrency;
        let ExpertActionBtnComponentId = expertChargeType + "_" + expert.PsychicId + "_" + expert.onlineStatus;
        let ReviewCount = expert.ReviewCount;
        let Expertonlinestatus = expert.onlineStatus;
        let Customerloginstatus = (gloggedInUserid > 0);
        let _renderBtn = "";
        let _renderMsgBtn = "";
        let RenderedBtnArr = [];
        let CallChatBtnStatus = "";
        let MsgEmailBtnStatus = "";
        let OfflineBtnStatus = "";
        let ChatButtonText = getLanguageKeyString('CWA_Chat_Button');
        let isFreeChat = config.isFreeChat;

        expert.freechatminute = (isFreeChat == false) ? 0 : expert.freechatminute;
		//expert listener work
        let ExpertListenerEnabled = (typeof expert.EnableExpertListListener !== 'undefined' && !isNaN(expert.EnableExpertListListener)) ? (parseInt(expert.EnableExpertListListener) == 1) : false;
        

        _renderBtn = ((expertChargeType == 3) ? "call" : "chat") + "-" + ((Customerloginstatus) ? "online" : "offline");
        _renderMsgBtn = "message" + "-" + ((Customerloginstatus) ? "online" : "offline");

        if (CallChatBtnShow) {
            MsgEmailBtnStatus = "hidden";
            OfflineBtnStatus = "hidden";
            CallChatBtnStatus = "";
        }
        else if (expert.onlineStatus == 2) {
            MsgEmailBtnStatus = "";
            CallChatBtnStatus = "hidden";
            OfflineBtnStatus = "hidden";
        } else {
            MsgEmailBtnStatus = "hidden";
            OfflineBtnStatus = "";
            CallChatBtnStatus = "hidden";
        }

        var _RenderMsgBtn = true;
        if (typeof isMyExpertPage === 'boolean' && isMyExpertPage === true) {
            _renderBtn = "view profile";
            _RenderMsgBtn = false;
        }

        switch (_renderBtn) {
            case "view profile":
                RenderedBtnArr.push('<a class="tn-block btn btn-primary chatbrown" role="button" onClick="window.location.href = expertLocaleUrl;" id="psychic-' + expert.PsychicId + '-link-btn" >View Profile</a>');
                break;
            case "chat-online":
               // let jsonobj = { "PsychicId": expert.PsychicId, "DisplayName": expert.DisplayName, "freechatminute": expert.freechatminute, "expertDetailsUrl": '', "expertChatCharge": expertCharge, "expertUSDChatCharge": expertUSDCharge, "expertChargeType": expertChargeType, "currency": currency, "ReviewCount": expert.ReviewCount, "Rating": expert.Rating, "isFree": isFreeChat };
                let jsonobj = {"PsychicId": expert.PsychicId, "expertFormatCharge": currency + expertCharge, "categoryName": categoryName, "expertPic": expertPic, "onlineStatus": expert.onlineStatus, "DisplayName": expert.DisplayName,
                    "freechatminute": expert.freechatminute, "expertDetailsUrl": expertLocaleUrl, "expertChatCharge": expertCharge, "expertUSDChatCharge": expertUSDCharge,
                    "expertChargeType": expertChargeType, "currency": currency, "ReviewCount": expert.ReviewCount, "Rating": expert.Rating, "isFree": isFreeChat, "IsChargeOverride": config.IsChargeOverride, "ModuleOfferType": config.ModuleOfferType, "NextTimeCount": config.NextTimeCount, "NextSessionCounter": config.NextSessionCounter, "moduleofferId": config.moduleofferId, "LowPriceOfferMin": config.LowPriceOfferMin
                }
                RenderedBtnArr.push('<a class="btn-block btn btn-primary chatbrown  letschat align-items-center ' + CallChatBtnStatus + '" role="button" onClick=\'trackChatClick("' + gPageName + '","' + expert.DisplayName + '",' + expert.PsychicId + ');ShowCustomerPrefrence(' + JSON.stringify(jsonobj) + ');\' id="psychic-' + expert.PsychicId + '-chat-btn">' + ChatButtonText + '</a><button class=" w-100 ml-0  btn  align-items-center" id="chatLoader-' + expert.PsychicId + '" type="button" style="display:none;" disabled="disabled"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + getLanguageKeyString("GLB_Initializing") + '</button>');
                break;
            case "chat-offline":
                RenderedBtnArr.push('<a class="btn-block btn btn-primary chatbrown  letschat  align-items-center ' + CallChatBtnStatus + '" role="button" data-toggle="modal" id="psychic-' + expert.PsychicId + '-chat-btn" data-target="#loginSignUp" onClick="trackChatClick(\'' + gPageName + '\',\'' + expert.DisplayName + '\',' + expert.PsychicId + ');fillSideBar(' + false + ', \'' + expert.DisplayName + '\', \'' + categoryName + '\', \'' + expertPic + '\', \'' + currency + expertCharge + '\', \'' + expert.Languages + '\', ' + expert.PsychicId + ', ' + expert.onlineStatus + ', ' + expert.freechatminute + ', ' + expert.expyear + ', \'chat\', \'' + expert.Slug + '\', ' + expertCharge + ', ' + expert.ReviewCount + ', ' + expert.Rating + ', ' + expertUSDCharge + ') ">' + ChatButtonText + '</a>');
                break;

        }

        //Send Message
        //if (_RenderMsgBtn) {
        //    if (expert.onlineStatus == 2) {
        //        RenderedBtnArr.push('<a class="btn btn-block btn-primary orangeborder emailus disabled align-items-center ' + MsgEmailBtnStatus + '" id="psychic-' + expert.PsychicId + '-email-btn" role ="button" data-toggle="modal" data-target="#loginSignUp" >' + getLanguageKeyString('CWA_Status_Busy') + '</a >');
        //    } else {
        //        RenderedBtnArr.push('<a class="btn btn-block btn-primary orangeborder emailus disabled align-items-center ' + MsgEmailBtnStatus + '" id="psychic-' + expert.PsychicId + '-email-btn" role ="button" data-toggle="modal" data-target="#loginSignUp" >' + getLanguageKeyString('CWA_Status_Busy') + '</a >');
        //        RenderedBtnArr.push('<a class="btn btn-block btn-primary orangeborder offline disabled align-items-center ' + MsgEmailBtnStatus + '" id="psychic-' + expert.PsychicId + '-offline-btn" role ="button" data-toggle="modal" data-target="#loginSignUp" >' + getLanguageKeyString('CWA_Status_Offline') + '</a >');
        //    }

        //}
        
        if (gloggedInUserid > 0) {
            RenderedBtnArr.push('<a class="btn btn-block btn-primary orangeborder emailus disabled align-items-center ' + MsgEmailBtnStatus + '" id="psychic-' + expert.PsychicId + '-email-btn" role ="button" data-toggle="modal" >' + getLanguageKeyString('CWA_Status_Busy') + '</a >');
            RenderedBtnArr.push('<a class="btn btn-block btn-primary orangeborder offline  align-items-center ' + OfflineBtnStatus + '" id="psychic-' + expert.PsychicId + '-offline-btn" role ="button" onclick="ShowSendMessageModal(\'' + expert.PsychicId + '\',\'' + expertPic + '\',\'' + expert.DisplayName + '\')" >' + getLanguageKeyString('CWA_Status_Offline') + '</a >');
        } else {
            RenderedBtnArr.push('<a class="btn btn-block btn-primary orangeborder emailus align-items-center btn-opacity ' + MsgEmailBtnStatus + '" id="psychic-' + expert.PsychicId + '-email-btn" role ="button" data-toggle="modal" data-target="#loginSignUp" onclick="openSignInDialog(\'true\')" >' + getLanguageKeyString('CWA_Status_Busy') + '</a >');
            RenderedBtnArr.push('<a class="btn btn-block btn-primary orangeborder offline align-items-center btn-opacity ' + OfflineBtnStatus + '" id="psychic-' + expert.PsychicId + '-offline-btn" role ="button" data-toggle="modal" data-target="#loginSignUp" onclick="openSignInDialog(\'true\')" >' + getLanguageKeyString('CWA_Status_Offline') + '</a >');
        }
        

        if (ExpertListenerEnabled) {
            addOnlineStatusListener(expert.PsychicId);
        }

        return '<div key="ExpertActionBtnComponentId" class="d-flex justify-content-end">' + RenderedBtnArr.join("") +'</div>';

    }

    getExpertSortData(expertdata, filterEnum) {
        switch (filterEnum) {
            case "1":
                expertdata.data = expertdata.data.sort((firstItem, secondItem) => firstItem.onlineStatus - secondItem.onlineStatus);
                return expertdata;
            case "2":
                //for oldest
                expertdata.data = expertdata.data.sort((secondItem, firstItem) => firstItem.PsychicId - secondItem.PsychicId);
                return expertdata;
            case "3":
                //for newest
                expertdata.data = expertdata.data.sort((firstItem, secondItem) => firstItem.PsychicId - secondItem.PsychicId);
                return expertdata;
            case "4":
                //for lowest price
                expertdata.data = expertdata.data.sort((firstItem, secondItem) => firstItem.ChargeRate * 100 - secondItem.ChargeRate * 100);
                return expertdata;
            case "5":
                //for highest price
                expertdata.data = expertdata.data.sort((secondItem, firstItem) => firstItem.ChargeRate * 100 - secondItem.ChargeRate * 100);
                return expertdata;
            case "6":
                //for ratings
                expertdata.data = expertdata.data.sort((secondItem, firstItem) => firstItem.Rating - secondItem.Rating);
                return expertdata;
            case "7":
                // for review highest review count
                expertdata.data = expertdata.data.sort((secondItem, firstItem) => firstItem.ReviewCount - secondItem.ReviewCount);
                return expertdata;
        }
    }

    renderFreeConsultationInfoBadge(offerUrl, currentLanguage) {
        let expertprofilepic = "https://cdn.anytimeastro.com/dashaspeaks/web/content/images/free-consultation-badge.png";
       /* if (!isOfferAvailable) {
            expertprofilepic = "https://cdn.anytimeastro.com/dashaspeaks/web/content/images/dis_free_cons.png";
        }*/
        let expertDisplayName = currentLanguage == "hi" ? "निःशुल्क सेवाएँ" : "Free Services";
        let renderHtml = '<div class="free-consultation-info-badge position-fixed"><a title="Free Consultation" onClick=\'freeConsultationClicked();\' href="' + offerUrl + '"><div class="img-badge"><img src="' + expertprofilepic + '" /><div class="content-badge"><p>' + expertDisplayName +'</p></div></div></a></div>';

        //document.body.innerHTML += renderHtml;
        document.getElementById("free-consultation-info-badge").innerHTML = renderHtml;
    }

    checkFreeConsultationInfoBadge(currentLanguage) {
        let culturelangcode = getUiCultureCode();
        let offerurl = gWebsitePrefix + culturelangcode + "free-services/" + url_string.replace(/&amp;/g, '&');
        //let offerurl2 = gWebsitePrefix + culturelangcode + "free-consultations/" + url_string.replace(/&amp;/g, '&');
        var offerObj = this;
        offerObj.renderFreeConsultationInfoBadge(offerurl, currentLanguage);
        /*if (isOfferAvailable) {
            offerObj.renderFreeConsultationInfoBadge(offerurl, currentLanguage, isOfferAvailable);
        } else {
            if (gloggedInUserid > 0) {
                INDXDBJSCONSULT.init(`agjf${gloggedInUserid}`, `ques${gloggedInUserid}`);
                INDXDBJSCONSULT.createTableOperations(false);

                INDXDBJSCONSULT.ReadQuestions(function (data) {
                    if (data != null && data.length > 0) {
                        offerObj.renderFreeConsultationInfoBadge(offerurl, currentLanguage, isOfferAvailable);
                    } else {
                        offerObj.renderFreeConsultationInfoBadge(offerurl2, currentLanguage, isOfferAvailable);
                    }
                });
            }
        }*/
        
        //document.getElementById("parentID").innerHTML += "new content"
        //document.body.appendChild(renderHtml);
    }

    getFreeConsulationCard(expertconfig, currentLanguage) {


        let AskGurujiBtnTxt = currentLanguage == "hi" ? "शुरू करें" : "Start Now";
        let expertCharge = _AIOfferPrice;
        let expertDisplayName = currentLanguage == "hi" ? "निःशुल्क परामर्श" : "FREE Consultation";
        let expertprofilepic = "https://cdn.anytimeastro.com/dashaspeaks/web/content/images/free-consultation-astro.png";
        let culturelangcode = getUiCultureCode();
        let expertUrl = gWebsitePrefix + culturelangcode + "free-consultation/" + url_string.replace(/&amp;/g, '&');
        const UserCurrency = expertconfig.UserCurrency == "USD" ? '$' : '₹';
        let RenderedBtnArr = [];
        let Customerloginstatus = (gloggedInUserid > 0);
        let isconsulatationofferapplicable = false;
        let showAItilewithOffer = false;
        let ModuleOfferType = 0;
        let moduleofferId = 0;
        var NextTimeCount = 0;
        var NextSessionCounter = 0;
        var lowPriceOffferMin = 0;
        if (checkIfOfferAvailable(eEligibleArea.CHAT)) {
            ModuleOfferType = parseInt(_CurrentOfferObj.OfferType);
            moduleofferId = parseInt(_CurrentOfferObj.OfferId);
            NextSessionCounter = _CurrentOfferObj.NextSessionCounter;
            NextTimeCount = _CurrentOfferObj.NextTimeCount;
            expertUrl += '&ofid=' + moduleofferId;
            //ModuleOfferType = 9;
            if (ModuleOfferType > 0 && ModuleOfferType == 7) {
                if (gloggedInUserid > 0) {
                    if (parseInt(moduleofferId) > 0) {
                        isconsulatationofferapplicable = true;
                    }
                } else {
                    isconsulatationofferapplicable = true;
                }
            }
            //else if (ModuleOfferType > 0 && ModuleOfferType == 8 && _gFreeExpertCounter == 0) {
            else if (ModuleOfferType > 0 && ModuleOfferType == 8) {
                showAItilewithOffer = true;
                if (gloggedInUserid > 0) {
                    if (parseInt(moduleofferId) > 0) {
                        isconsulatationofferapplicable = true;
                    }
                } else {
                    isconsulatationofferapplicable = true;
                }
            }
            else if (ModuleOfferType > 0 && ModuleOfferType == 9) {
                
                if (gloggedInUserid > 0) {
                    if (parseInt(moduleofferId) > 0) {
                        isconsulatationofferapplicable = true;
                    }
                } else {
                    isconsulatationofferapplicable = true;
                }
            }
        }
        if (parseInt(_gIsFreeConsultOffrAvailable) > 0) {
            this.checkFreeConsultationInfoBadge(currentLanguage);
        }

        if ((!isconsulatationofferapplicable || !_isAITiletoShow)) {
            return "";
        }
        
        if (Customerloginstatus) {
            RenderedBtnArr.push('<a class="btn-block btn btn-primary chatbrown  letschat align-items-center " role="button" onClick=\'trackChatClick("' + gPageName + '","Free Consultation",0);freeConsultationClicked();\' href="' + expertUrl + '">' + AskGurujiBtnTxt + '</a><button class=" w-100 ml-0  btn  align-items-center" type="button" style="display:none;" disabled="disabled"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + getLanguageKeyString("GLB_Initializing") + '</button>');
        } else {
            RenderedBtnArr.push('<a class="btn-block btn btn-primary chatbrown  letschat  align-items-center " role="button" data-toggle="modal" data-target="#loginSignUp" onClick="trackChatClick(\'' + gPageName + '\',\'Free Consultation\',0);openSignInDialog(\'true\')">' + AskGurujiBtnTxt + '</a>');
        }

        let expertstatusbadge = '<div class="status-badge specific-Clr-Online" title="Online"></div><div class="status-badge-txt text-center specific-Clr-Online"><span id="" title="Online" class="status-badge-txt specific-Clr-Online tooltipex">Online</span></div>';
        
        const categoryName = currentLanguage == "hi" ? "वैदिक ज्योतिष, AI, KP, टैरो" : "Vedic Astrology, AI, KP, Tarot";
        let ExpertLanguages = currentLanguage == "hi" ? "इंग्लिश, हिन्दी" : "English, Hindi";
        let askGurujiRateTxt = currentLanguage == "hi" ? "शीर्ष रेटेड" : "Top Rated";
        let expertChargeBeforeDiscountStr = '<del>' + UserCurrency + '' + expertCharge + '</del>';
        let itemsection1 = '<ul class="list-unstyled d-flex mb-0"><li class = "mr-3 position-relative psychic-presence status-online" data-status="online"  ><a href="javascript:void(0);" data-href="' + expertUrl + '"><div class="psyich-img position-relative"><img src="' + expertprofilepic + '" width="80" height="80" style="border-radius:50%;" loading="lazy" /></div></a>' + expertstatusbadge + '</li ><li class="w-100 overflow-hidden"><a href="javascript:void(0);" data-href="' + expertUrl + '" class="colorblack font-weight-semi font16 mt-0 ml-0 mr-0 mb-0 p-0 text-capitalize d-block" data-toggle="tooltip" title="">' + expertDisplayName + '</a><span class="font-12 d-block color-red">' + categoryName + '</span><span class="font-12 d-block exp-language">' + ExpertLanguages + '</span><span class="font-12 font-weight-semi-bold d-flex"> <span class="exprt-price">' + expertChargeBeforeDiscountStr + getLanguageKeyString("EP_Charge_Min") + '</span></span></li></ul >';


        let itemsection2 = '<div class="d-flex align-items-center justify-content-between"><div class="psy-review-section" ><a href="javascript:void(0);" data-href="' + expertUrl + '"><p class="m-0 p-0 font9 nowrap review-psy"><span class="colorblack font-12 m-0 p-0 d-block"><span class="font-12 font-weight-bold m-0 p-0 color-brown">' + askGurujiRateTxt + '</span></span > <i data-star="5"></i></p></a></div><div class="d-flex align-items-end position-relative"><div class="d-block">'+RenderedBtnArr.join("")+'<span class="font-11 d-block text-uppercase font-weight-semi-bold text-center text-secondary" style="margin-top:2px;">' + getLanguageKeyString("GLB_Free_Txt") +'</span></div></div></div>';

        let AIOffferClass = "";
        if (showAItilewithOffer && _gFreeExpertCounter > 0) {
            AIOffferClass = "d-none";
        }

        return '<div id="ATAAIOfferTile" class="psychic-card overflow-hidden expertOnline ask-guruji ' + AIOffferClass+'" >' + itemsection1 + itemsection2 +'</div>';
    }

    getPsychicCardRecommended(expertdata, expertconfig) {
        var IsFreeChat = false;
        const expertCDNPath = cDNBaseUrl;
        //mhtml += mdata.data[i]['DisplayName'];
        //   let expertprofilepic = "" + cDNBaseUrl + "/web/content/images/no-expert-pic.png";
        let expertprofilepic = expertCDNPath + "/web/content/images/no-expert-pic.png";
        if (typeof expertdata.Picture != "undefined" && expertdata.Picture != null && expertdata.Picture != "") {

            expertprofilepic = "https://cdn.anytimeastro.com/dashaspeaks/psychics/" + expertdata.Picture + "";
        }

        const UserCurrency = expertconfig.UserCurrency == "USD" ? '$' : '₹';
        let expertCharge = 0;
        let BaseRate = 0;
        let DiscountPer = 0;
        let IsChargeOverride = false;
        let ExpertListenerEnabled = (typeof expertdata.EnableExpertListListener !== 'undefined' && !isNaN(expertdata.EnableExpertListListener)) ? (parseInt(expertdata.EnableExpertListListener) == 1) : false;

        if (expertconfig.UserCurrency == "INR") {
            expertdata.ChargeRate = Math.ceil(expertdata.ChargeRate);
            expertdata.BaseRate = Math.ceil(expertdata.BaseRate);
        } else {
            expertdata.ChargeRate = expertdata.ChargeRate.toFixed(2);
            expertdata.BaseRate = expertdata.BaseRate.toFixed(2);
        }


        expertCharge = expertdata.ChargeRate;
        BaseRate = expertdata.BaseRate;
        DiscountPer = expertdata.DiscountPer;
        IsChargeOverride = expertdata.IsChargeOverride;

        let expertChargeToShow = expertCharge;
        let culturelangcode = getUiCultureCode();
        let expertUrl = gWebsitePrefix + culturelangcode + "experts/" + expertdata.Slug + "/" + url_string.replace(/&amp;/g, '&');
        let walletRechargeUrl = gWebsitePrefix + culturelangcode + "walletrecharge/" + url_string.replace(/&amp;/g, '&');
        let expertChargeBeforeDiscount = 0;

        let expertChargeBeforeDiscountStr = "";
        let OnlineButtonText = "Chat";
        let BusyButtonText = "Busy";
        let OfflineButtonText = "Offline";
        let isofferapplicable = '';
        //console.log(expertdata);
        let localModuleOfferType = 0;
        let lowPriceOffferMin = 0;
        let islowpriceoffer = false;
        if (checkIfOfferAvailable(eEligibleArea.CHAT)) {
            
            localModuleOfferType = parseInt(_CurrentOfferObj.OfferType);
            let localmoduleofferId = _CurrentOfferObj.OfferId;


            // ModuleOfferType = 9;
            let IsExpertMarkedFree = expertdata.showinfree;


            if (localModuleOfferType > 0 && (localModuleOfferType == 5 || localModuleOfferType == 8 || localModuleOfferType == 9)) {
                if (expertdata.freechatminute > 0 && IsExpertMarkedFree == 1) {
                    if (gloggedInUserid > 0) {
                        if (parseInt(localmoduleofferId) > 0) {
                            isofferapplicable = 'offer-applicable';
                            IsFreeChat = true;
                            expertChargeToShow = BaseRate;
                        }
                    } else {
                        isofferapplicable = 'offer-applicable';
                        IsFreeChat = true;
                        expertChargeToShow = BaseRate;
                    }
                }
            }
            else if (localModuleOfferType > 0 && (localModuleOfferType == 6)) {
                if (expertdata.freechatminute > 0 &&  IsExpertMarkedFree == 1) {
                    if (gloggedInUserid > 0) {
                        if (parseInt(localmoduleofferId) > 0) {
                            isrechargeofferavailable = true;
                            IsFreeChat = true;
                        }
                    } else {
                        isrechargeofferavailable = true;
                        IsFreeChat = true
                    }
                }
            }
            else if (localModuleOfferType > 0 && (localModuleOfferType == 10)) {
                lowPriceOffferMin = _CurrentOfferObj.OfferMinutes;
                let _userDealCount = 0;
                if (expertconfig.purchaseddeals.length > 0) {
                    let expertDealsArray = expertconfig.purchaseddeals.filter(e => {
                        return e.ExpertId == expertdata.PsychicId && e.ChargeType == 1;
                    })
                    _userDealCount = expertDealsArray.length;
                }

                if (_userDealCount < 1) {
                    IsChargeOverride = false;
                    if (expertdata.IsLowPrice > 0 && _discountedOfferPrice > 0 && expertdata.showinfree > 0) {
                        //discountedOfferPrice = 5;
                        if (gloggedInUserid > 0) {
                            if (parseInt(localmoduleofferId) > 0) {
                                expertChargeToShow = _discountedOfferPrice;
                                islowpriceoffer = true;
                                //expertCharge = _discountedOfferPrice;
                            }
                        } else {
                            expertChargeToShow = _discountedOfferPrice;
                            islowpriceoffer = true;
                            //expertCharge = _discountedOfferPrice;
                        }
                    }
                }
            }
        }
        if (IsFreeChat || islowpriceoffer) {
            if (expertUrl.indexOf("?") >= 0) {
                if (expertUrl.lastIndexOf("?") == expertUrl.length - 1) {
                    expertUrl += "fcm=true";
                } else if (expertUrl.indexOf("fcm") == -1) {
                    expertUrl += "&fcm=true";
                }
            } else {
                expertUrl += "?fcm=true";
            }
        }

        if (expertUrl.indexOf("?") >= 0) {
            if (expertUrl.lastIndexOf("?") == expertUrl.length - 1) {
                expertUrl += "opaction=chat";
            } else if (expertUrl.indexOf("opaction") == -1) {
                expertUrl += "&opaction=chat";
            }
        } else {
            expertUrl += "?opaction=chat";
        }


        if (walletRechargeUrl.indexOf("?") >= 0) {
            if (walletRechargeUrl.lastIndexOf("?") == walletRechargeUrl.length - 1) {
                walletRechargeUrl += "opaction=chat&eid=" + expertdata.PsychicId;
            } else {
                if (walletRechargeUrl.indexOf("opaction") == -1) {
                    walletRechargeUrl += "&opaction=chat";
                }
                if (walletRechargeUrl.indexOf("eid") == -1) {
                    walletRechargeUrl += "&eid=" + expertdata.PsychicId;
                }
            }
        } else {
            walletRechargeUrl += "?opaction=chat&eid=" + expertdata.PsychicId;
        }

        if (BaseRate > 0 && ((DiscountPer > 0 && !IsChargeOverride) || (BaseRate > expertChargeToShow && IsChargeOverride) || (BaseRate > expertChargeToShow)) && !IsFreeChat && localModuleOfferType != 6) {
            expertChargeBeforeDiscount = BaseRate;
            expertChargeBeforeDiscountStr = '<del>' + UserCurrency + '' + expertChargeBeforeDiscount + '</del>';
        }
        if (ExpertListenerEnabled) {
            addOnlineStatusListener(expertdata.PsychicId);
        }
        let expertstatus = expertdata.onlineStatus == 1 ? "Available" : (expertdata.onlineStatus == 2 ? "Busy" : "Unavailable");

        return '<div class="item p-3 mb-3 expertOnline bg-white ' + isofferapplicable + '" data-psychic-id=' + expertdata.PsychicId + '><a href="javascript:void(0);" data-href="' + expertUrl + '"><div class="psychic-presence status-' + expertdata.PsychicId + '" data-status=' + expertstatus + ' data-psychic-id="' + expertdata.PsychicId + '"><div id="psychic-' + expertdata.PsychicId + '-badge" class="status-badge ' + expertstatus + '" title=""></div></div><div class="astro-profile"><div><img src="' + expertprofilepic + '" class="img-fluid" loading="lazy"></div><p class="astro-name pt-2 mb-1 font-weight-semi-bold text-center colorblack text-capitalize" data-toggle="tooltip" title="' + expertdata.DisplayName + '" style="white-space: nowrap;text-overflow: ellipsis;display: block;overflow:hidden">' + expertdata.DisplayName + '</p></div><p class="font-12 font-weight-semi-bold mb-1 d-flex align-items-center justify-content-center"> <span class="exprt-price">' + expertChargeBeforeDiscountStr + ' ' + UserCurrency + expertChargeToShow + ' ' + getLanguageKeyString("EP_Charge_Min") + '</span> <span class="free-badge text-uppercase color-red ml-2 d-none">' + getLanguageKeyString("GLB_Free_Txt") + '</span></p></a><a href="javascript:void(0);" data-href="' + expertUrl + '" class="btn-block btn btn-primary chatbrown  letschat align-items-center" role="button" onClick=\'trackChatClick("' + gPageName + '","' + expertdata.DisplayName + '",' + expertdata.PsychicId + ')\' id="psychic-' + expertdata.PsychicId + '-chat-btn">' + OnlineButtonText + '</a><a href="javascript:void(0);" data-href="' + expertUrl + '" class="btn-block btn btn-primary chatbrown  emailus align-items-center hidden" role="button" onClick=\'trackChatClick("' + gPageName + '","' + expertdata.DisplayName + '",' + expertdata.PsychicId + ')\' id="psychic-' + expertdata.PsychicId + '-email-btn">' + BusyButtonText + '</a><a href="javascript:void(0);" data-href="' + expertUrl + '" class="btn-block btn btn-primary chatbrown  offline align-items-center hidden" role="button" onClick=\'trackChatClick("' + gPageName + '","' + expertdata.DisplayName + '",' + expertdata.PsychicId + ')\' id="psychic-' + expertdata.PsychicId + '-offline-btn">' + OfflineButtonText + '</a></div>';
    }



    getPsychicCard(expertdata, expertconfig) {        
        var IsFreeChat = false;
        const expertCDNPath = cDNBaseUrl;
        //mhtml += mdata.data[i]['DisplayName'];
        //   let expertprofilepic = "" + cDNBaseUrl + "/web/content/images/no-expert-pic.png";
        let expertprofilepic = expertCDNPath + "/web/content/images/no-expert-pic.png";
        if (typeof expertdata.Picture != "undefined" && expertdata.Picture != null && expertdata.Picture != "") {

            expertprofilepic = "https://cdn.anytimeastro.com/dashaspeaks/psychics/" + expertdata.Picture + "";
        }

        const UserCurrency = expertconfig.UserCurrency == "USD" ? '$' : '₹';

        let lpSessionDiscount = expertconfig.PriceDiscount;
        let expertCharge = 0;
        let expertCallCharge = 0;
        let BaseRate = 0;
        let DiscountPer = 0;
        let IsChargeOverride = false;

        if (expertconfig.UserCurrency == "INR") {
            expertdata.ChargeRate = Math.ceil(expertdata.ChargeRate);
            expertdata.BaseRate = Math.ceil(expertdata.BaseRate);
        } else {
            expertdata.ChargeRate = expertdata.ChargeRate.toFixed(2);
            expertdata.BaseRate = expertdata.BaseRate.toFixed(2);
        }


        expertCharge = expertdata.ChargeRate;
        expertCallCharge = expertdata.ChargeRate;
        BaseRate = expertdata.BaseRate;
        DiscountPer = expertdata.DiscountPer;
        IsChargeOverride = expertdata.IsChargeOverride;

        let expertChargeToShow = expertCharge;
        let expertChargeType = 1
        let _BtnToRender  = (expertChargeType == 3) ? "call" : "chat";
        let CallChatBtnShow = (expertdata.onlineStatus == 1) ? true : false;
        let culturelangcode = getUiCultureCode();
        let expertUrl = gWebsitePrefix + culturelangcode + "experts/" + expertdata.Slug + "/" + url_string.replace(/&amp;/g, '&');
        let walletRechargeUrl = gWebsitePrefix + culturelangcode + "walletrecharge/" + url_string.replace(/&amp;/g, '&');
        let expertChargeBeforeDiscount = 0;

        let expertChargeBeforeDiscountStr = "";
        let FreeMinutesForEligibleOffer = 0;
        
        let isofferapplicable = '';
        let isrechargeofferavailable = false;
        //console.log(expertdata);
        let ModuleOfferType = 0;
        let moduleofferId = 0;
        let NextTimeCount = 0;
        let NextSessionCounter = 0;
        let lowPriceOffferMin = 0;
        let islowpriceoffer = false;

        if (checkIfOfferAvailable(eEligibleArea.CHAT)) {
            if (_CurrentOfferObj.OfferType > 0) {
                let ifanyofferavailableforUser = false;
                let localModuleOfferType = parseInt(_CurrentOfferObj.OfferType);
                let localmoduleofferId = _CurrentOfferObj.OfferId;
                
                //ModuleOfferType = 10;
                let IsExpertMarkedFree = expertdata.showinfree;
                //FreeMinutesForEligibleOffer = expertdata.freechatminute;

                if (localModuleOfferType > 0 && (localModuleOfferType == 5 || localModuleOfferType == 8 || localModuleOfferType == 9)) {
                    
                    if (expertdata.freechatminute > 0 && IsExpertMarkedFree == 1) {
                        ifanyofferavailableforUser = true;
                        FreeMinutesForEligibleOffer = expertdata.freechatminute;
                        if (gloggedInUserid > 0) {
                            if (parseInt(localmoduleofferId) > 0) {
                                isofferapplicable = 'offer-applicable';
                                IsFreeChat = true;
                                expertChargeToShow = BaseRate;
                            }
                        } else {
                            isofferapplicable = 'offer-applicable';
                            IsFreeChat = true;
                            expertChargeToShow = BaseRate;
                        }
                    }
                }
                else if (localModuleOfferType > 0 && (localModuleOfferType == 6)) {
                    
                    if (expertdata.freechatminute > 0 && IsExpertMarkedFree == 1) {
                        ifanyofferavailableforUser = true;
                        FreeMinutesForEligibleOffer = expertdata.freechatminute;
                        if (gloggedInUserid > 0) {
                            if (parseInt(localmoduleofferId) > 0) {
                                isrechargeofferavailable = true;
                                IsFreeChat = true;
                            }
                        } else {
                            isrechargeofferavailable = true;
                            IsFreeChat = true
                        }
                    }
                }
                else if (localModuleOfferType > 0 && (localModuleOfferType == 10)) {
                    lowPriceOffferMin = _CurrentOfferObj.OfferMinutes;
                    let _userDealCount = 0;
                    if (expertconfig.purchaseddeals.length > 0) {
                        let expertDealsArray = expertconfig.purchaseddeals.filter(e => {
                            return e.ExpertId == expertdata.PsychicId && e.ChargeType == 1;
                        })
                        _userDealCount = expertDealsArray.length;
                    }

                    if (_userDealCount < 1) {
                        IsChargeOverride = false;
                        if (expertdata.IsLowPrice > 0 && _discountedOfferPrice > 0 && expertdata.showinfree > 0) {
                            ifanyofferavailableforUser = true;
                            //discountedOfferPrice = 5;
                            if (gloggedInUserid > 0) {
                                if (parseInt(localmoduleofferId) > 0) {
                                    expertChargeToShow = _discountedOfferPrice;
                                    islowpriceoffer = true;
                                   //expertCharge = _discountedOfferPrice;
                                }
                            } else {
                                expertChargeToShow = _discountedOfferPrice;
                                islowpriceoffer = true;
                               // expertCharge = _discountedOfferPrice;
                            }
                        }
                    }
                }


                if (ifanyofferavailableforUser) {
                    ModuleOfferType = localModuleOfferType;
                    moduleofferId = localmoduleofferId;
                    NextTimeCount = _CurrentOfferObj.NextTimeCount;
                    NextSessionCounter = _CurrentOfferObj.NextSessionCounter;
                    
                }
            }
            
        }

        


        if (IsFreeChat || islowpriceoffer) {
            if (expertUrl.indexOf("?") >= 0) {
                if (expertUrl.lastIndexOf("?") == expertUrl.length - 1) {
                    expertUrl += "fcm=true";
                } else if (expertUrl.indexOf("fcm") == -1) {
                    expertUrl += "&fcm=true";
                }
            } else {
                expertUrl += "?fcm=true";
            }
        }


        if (walletRechargeUrl.indexOf("?") >= 0) {
            if (walletRechargeUrl.lastIndexOf("?") == walletRechargeUrl.length - 1) {
                walletRechargeUrl += "opaction=chat&eid=" + expertdata.PsychicId;
            } else {
                if (walletRechargeUrl.indexOf("opaction") == -1) {
                    walletRechargeUrl += "&opaction=chat";
                }
                if (walletRechargeUrl.indexOf("eid") == -1) {
                    walletRechargeUrl += "&eid=" + expertdata.PsychicId;
                }
            }
        } else {
            walletRechargeUrl += "?opaction=chat&eid=" + expertdata.PsychicId;
        }

        if (BaseRate > 0 && ((DiscountPer > 0 && !IsChargeOverride) || (BaseRate > expertChargeToShow && IsChargeOverride) || (BaseRate > expertChargeToShow)) && !IsFreeChat && ModuleOfferType != 6 ) {
            expertChargeBeforeDiscount = BaseRate;
            expertChargeBeforeDiscountStr = '<del>' + UserCurrency + '' + expertChargeBeforeDiscount + '</del>';
        }
        

        let expertstatus = expertdata.onlineStatus == 1 ? "Available" : (expertdata.onlineStatus == 2 ? "Busy" : "Unavailable");
        let expertstatusbadge = '<div id="psychic-' + expertdata.PsychicId + '-badge" class="status-badge specific-Clr-Busy" title="Busy"></div><div  class="status-badge-txt text-center specific-Clr-Busy"><span id="psychic-' + expertdata.PsychicId + '-badge-txt"></span></div>';
        if (expertdata.onlineStatus == 1) {
            expertstatusbadge = '<div id="psychic-' + expertdata.PsychicId + '-badge" class="status-badge specific-Clr-Online" title="Online"></div><div  class="status-badge-txt text-center specific-Clr-Online"><span id="psychic-' + expertdata.PsychicId + '-badge-txt"></span></div>';
        } else if (expertdata.onlineStatus == 3) {
            expertstatusbadge = '<div id="psychic-' + expertdata.PsychicId + '-badge" class="status-badge specific-Clr-Offline" title="Offline"></div><div  class="status-badge-txt text-center specific-Clr-Offline"><span id="psychic-' + expertdata.PsychicId + '-badge-txt"></span></div>';
        }


        var sExpertCategories = GetCatName(expertdata.CategoryId);

        var sExpertCategoriesSplitted = sExpertCategories;//.substr(0, 20) + (sExpertCategories.length > 17 ? "..." : "");
        const categoryName = sExpertCategoriesSplitted;
        const CategoryId = expertdata.CategoryId;

        let OriginalLanguage = getExpertLanguages(expertdata.Languages);
        let ExpertLanguages = OriginalLanguage;

        let expertExperience = expertdata.expyear <= 0 ? 1 : expertdata.expyear;
        //const cls = expertdata.IsFavorite ? "fa fa-heart" : "fa fa-heart-o";
        //const isFavorite = expertdata.IsFavorite == true ? "red" : "grey";

        let dealsection = "";
        let _userDealCountSection = "";
        let _userDealCount = 0;
        let usrActionTxt = "data-toggle=modal data-target=#loginSignUp onclick=openSignInDialog('true')";
        if (expertdata.hasdeal) {
            if (gloggedInUserid > 0) {
                usrActionTxt = "onclick=\"SetBeforePaymentPrefrencesForDeal('" + walletRechargeUrl + "','chat', 0," + expertdata.PsychicId + ",'" + expertprofilepic + "','" + expertdata.DisplayName + "','" + UserCurrency + "','" + expertUrl +"')\"";
                if (expertconfig.purchaseddeals.length > 0) {
                    let expertDealsArray = expertconfig.purchaseddeals.filter(e => {
                        return e.ExpertId == expertdata.PsychicId && e.ChargeType==1;
                    })
                    _userDealCount = expertDealsArray.length;

                }

                if (_userDealCount > 0) {
                    _userDealCountSection = '<span class="position-absolute font-weight-bold d-flex align-items-center bg-red justify-content-center" style="width:14px; height:14px; top:-5px; right:-3px;border-radius:100%; font-size: 8px!important; line-height: 2px;">' + _userDealCount + '</span>';
                }
            }


            dealsection = '<div class="expert-deal-badge position-relative mr-1" style=" width:48px; height:15px;"><a href="javascript:void(0);" ' + usrActionTxt +'><span style="background:#5bbe2a; line-height:15px;" class="text-white rounded d-flex px-1 font-12 align-items-center pr-4">Deal <img src="https://cdn.anytimeastro.com/dashaspeaks/web/content/images/deal-box.gif" width="14" height="16" class="position-absolute" style="top:-5px; right:4px; width:14px !important;">' + _userDealCountSection + '</span></a></div>';
        }




        let itemsection1 = '<ul class="list-unstyled d-flex mb-0"><li class = "mr-3 position-relative psychic-presence status-' + expertdata.PsychicId + '" data-status="' + expertstatus + '" data-chata="' + UserCurrency + '' + expertCharge + '" data-calla="' + UserCurrency + ' ' + expertCallCharge + '" ><a href="javascript:void(0);" data-href="' + expertUrl + '"><div class="psyich-img position-relative"><img src="' + expertprofilepic + '" width="80" height="80" style="border-radius:50%;" loading="lazy" /></div></a>' + expertstatusbadge + '</li ><li class="w-100 overflow-hidden"><a href="javascript:void(0);" data-href="' + expertUrl + '" class="colorblack font-weight-semi font16 mt-0 ml-0 mr-0 mb-0 p-0 text-capitalize d-block" data-toggle="tooltip" title="' + expertdata.DisplayName + '">' + expertdata.DisplayName + '</a><span class="font-12 d-block color-red">' + categoryName + '</span><span class="font-12 d-block exp-language">' + ExpertLanguages + '</span><span class="font-12 d-block"> ' + getLanguageKeyString("EP_Exp") + ' : ' + expertExperience + ' ' + getLanguageKeyString("EP_ExpYear") + '</span><span class="font-12 font-weight-semi-bold d-flex"> <span class="exprt-price">' + expertChargeBeforeDiscountStr +' ' + UserCurrency + expertChargeToShow + ' ' + getLanguageKeyString("EP_Charge_Min") + '</span> <span class="free-badge text-uppercase color-red ml-2 d-none">' + getLanguageKeyString("GLB_Free_Txt") + '</span></span></li></ul >';


        let itemsection2 = '<div class="d-flex align-items-center justify-content-between"><div class="psy-review-section" ><a href="javascript:void(0);" data-href="' + expertUrl + '"><p class="m-0 p-0 font9 nowrap review-psy">' + this.getReviews(expertdata.Rating, expertdata.ReviewCount) + '</p></a></div><div class="d-flex align-items-end position-relative"><div class="d-block">' + this.ExpertActionBtn(expertdata, {
            "CallChatBtnShow": CallChatBtnShow, "categoryName": CategoryId, "expertCharge": expertCharge, "expertUSDCharge": expertdata.Charge, "expertChargeToShow": expertChargeToShow, "UserCurrency": UserCurrency, "expertChargeType": expertChargeType, "expertlocaleurl": expertUrl, "isFreeChat": IsFreeChat, "IsChargeOverride": IsChargeOverride, "ModuleOfferType": ModuleOfferType, "NextTimeCount": NextTimeCount, "NextSessionCounter": NextSessionCounter, "moduleofferId": moduleofferId, "LowPriceOfferMin": lowPriceOffferMin }) + '' + this.getFreeMinuteText(isrechargeofferavailable, FreeMinutesForEligibleOffer) +'</div></div></div>';


        let itemsection3 = '<div class="checkmark">' + dealsection +'</div >';

        return '<div class="psychic-card overflow-hidden   expertOnline ' + isofferapplicable + ' ' + (expertdata.Badge > 0 ? 'must-try-badge-applicable' : '') + '" data-psychic-id="' + expertdata.PsychicId + '">' + this.getMustTryBadge(expertdata.badgeid) + itemsection1 + itemsection2 + itemsection3 + '</div>';
    }

    performEidsInjection(groupData, settingsjson) {
        if ((groupData != null && groupData.length > 0)) {
            let finalgroupFilterData = [];
            
            let onlineAstrologersList = groupData.filter(e => {
                return e.onlineStatus == 1
            });
            //console.log("before grouping Online Astrologers", shuffledOnlineAstrologers);
            //Shuffled Offline Astrologer
            let offBusyAstrologersList = groupData.filter(value => !onlineAstrologersList.includes(value));

            if (settingsjson.settings.hasOwnProperty("inject")) {
                if (settingsjson.settings.inject.hasOwnProperty("eids")) {
                    let expertidsLength = settingsjson.settings.inject.eids;
                    if (expertidsLength.length > 0) {
                        for (var eids = 0; eids < expertidsLength.length; eids++) {

                            let filteredGroupDatafromOnline = onlineAstrologersList.filter(e => { return e.PsychicId == expertidsLength[eids].eid });

                            if (filteredGroupDatafromOnline.length > 0) {
                                let filteredOnlineAstrologersList = onlineAstrologersList.filter(function (item) {
                                    return filteredGroupDatafromOnline.filter(function (item2) {
                                        return item.PsychicId == item2.PsychicId;
                                    }).length == 0;
                                });

                                let minRange = 0
                                let maxRange = 0

                                if (expertidsLength[eids].rng != "") {
                                    let groupsortarr = this.calculateMinMaxRange(expertidsLength[eids].rng);
                                    if (groupsortarr.length > 1) {
                                        minRange = parseInt(groupsortarr[0]);
                                        maxRange = parseInt(groupsortarr[1]);
                                        if (onlineAstrologersList.length <= maxRange) {
                                            maxRange = onlineAstrologersList.length;
                                        }
                                    } else {
                                        minRange = maxRange = parseInt(groupsortarr[0]);
                                    }
                                }

                                let randomnumber = this.randomIntFromInterval(minRange, maxRange);
                                //traceLog("random number eids", randomnumber);

                                filteredOnlineAstrologersList.splice(randomnumber, 0, filteredGroupDatafromOnline[0]);
                                onlineAstrologersList = filteredOnlineAstrologersList;
                            }
                        }
                    }
                }
            }
            Array.prototype.push.apply(finalgroupFilterData, onlineAstrologersList);
            Array.prototype.push.apply(finalgroupFilterData, offBusyAstrologersList);
            return finalgroupFilterData;
        }
        return groupData;
    }

    performPriorityInjection(groupData, settingsjson) {
        if ((groupData != null && groupData.length > 0)) {
            let finalgroupFilterData = [];

            let onlineAstrologersList = groupData.filter(e => {
                return e.onlineStatus == 1
            });
            //console.log("before grouping Online Astrologers", shuffledOnlineAstrologers);
            //Shuffled Offline Astrologer
            let offBusyAstrologersList = groupData.filter(value => !onlineAstrologersList.includes(value));
            if (settingsjson != null && typeof settingsjson === "object" && settingsjson.hasOwnProperty("priority")) {
                if (typeof settingsjson.priority === "object") {
                    let expertPriorityArray = settingsjson.priority;
                    if (expertPriorityArray.length > 0) {
                        for (var pri = 0; pri < expertPriorityArray.length; pri++) {

                            if (expertPriorityArray[pri].hasOwnProperty("eids") && typeof expertPriorityArray[pri].eids === 'object') {
                                let eidsArray = expertPriorityArray[pri].eids;
                                //filter eidsarray data from Online Astrologer
                                let eidsFilteredOnlineArray = onlineAstrologersList.filter(function (item) {
                                    return eidsArray.filter(function (item2) {
                                        return item.PsychicId == item2;
                                    }).length == 1;
                                });

                                //shuffled eidsFilteredOnlineArray
                                if (expertPriorityArray[pri].hasOwnProperty("shf")) {
                                    if ((typeof expertPriorityArray[pri].shf === 'string' || typeof expertPriorityArray[pri].shf === 'number') && parseInt(expertPriorityArray[pri].shf) > 0) {
                                        eidsFilteredOnlineArray = eidsFilteredOnlineArray.sort(() => Math.random() - 0.5)
                                    }
                                }

                                let totalAstrologers = 0;
                                let factorialValue = 1;
                                let priorityWiseAstrologersList = [];
                                //pop total records from eidsFilteredOnlineArray and pushed to anotehr array priorityWiseAstrologersList.
                                if (expertPriorityArray[pri].hasOwnProperty("tot")) {
                                    if ((typeof expertPriorityArray[pri].tot === 'string' || typeof expertPriorityArray[pri].tot === 'number') && parseInt(expertPriorityArray[pri].tot) > 0) {
                                        totalAstrologers = parseInt(expertPriorityArray[pri].tot);
                                        if (eidsFilteredOnlineArray.length <= totalAstrologers) {
                                            totalAstrologers = eidsFilteredOnlineArray.length;
                                        }
                                        Array.prototype.push.apply(priorityWiseAstrologersList, eidsFilteredOnlineArray.splice(0, totalAstrologers))
                                    }
                                }

                                //reshuffled eidsFilteredOnlineArray (remaining items)
                                if (expertPriorityArray[pri].hasOwnProperty("rsf")) {
                                    if ((typeof expertPriorityArray[pri].rsf === 'string' || typeof expertPriorityArray[pri].rsf === 'number') && parseInt(expertPriorityArray[pri].rsf) > 0) {
                                          eidsFilteredOnlineArray = eidsFilteredOnlineArray.sort(() => Math.random() - 0.5);
                                    }
                                }
                                // factorial records = factorialrecords - totalrecords;
                                //pop total factorial records from eidsFilteredOnlineArray and pushed to anotehr array priorityWiseAstrologersList.
                                if (expertPriorityArray[pri].hasOwnProperty("fac")) {
                                    if ((typeof expertPriorityArray[pri].fac === 'string' || typeof expertPriorityArray[pri].fac === 'number') && parseInt(expertPriorityArray[pri].fac) > 1) {
                                        factorialValue = parseFloat(expertPriorityArray[pri].fac);
                                        let factTotalAstrologers = parseInt(totalAstrologers * factorialValue) - totalAstrologers;
                                        Array.prototype.push.apply(priorityWiseAstrologersList, eidsFilteredOnlineArray.splice(0, factTotalAstrologers));
                                    }
                                }
                                //final list after priority injection.
                                if (priorityWiseAstrologersList.length > 0) {

                                    onlineAstrologersList = onlineAstrologersList.filter(value => !priorityWiseAstrologersList.includes(value));

                                    Array.prototype.push.apply(finalgroupFilterData, priorityWiseAstrologersList);
                                   
                                }
                            }
                        }

                    }
                }
            }
            Array.prototype.push.apply(finalgroupFilterData, onlineAstrologersList);
            Array.prototype.push.apply(finalgroupFilterData, offBusyAstrologersList);
            return finalgroupFilterData;
        }

        return groupData;
        
    }

    checkExpertsFreeSessionTime(expertId, cb) {
        //console.time("freeexpertlist");
        if (typeof firebase !== 'undefined') {
            var databaseRef = firebase.database().ref();
            if (databaseRef) {
                var expFreeListRef = databaseRef.child('upd').child('elct');
                // console.log(expFreeListRef);
                let expertFreeTimePath = "upd/elct";
                if (typeof expertId === 'number' && parseInt(expertId) > 0) {
                    expertFreeTimePath += "/" + expertId;
                }
                firebase.database().ref(expertFreeTimePath).once("value", function (snapshot) {
                    var expertFreeTimeData = snapshot.val();
                    var responseData;
                    if (typeof expertFreeTimeData === "number" && expertFreeTimeData > 0) {
                        responseData = {};
                        responseData[expertId] = expertFreeTimeData;
                        //  console.log("expertFreeTimeData => ", expertFreeTimeData);
                    } else {
                        responseData = expertFreeTimeData;
                    }
                   // console.timeEnd("freeexpertlist");
                    cb(responseData);
                });

            }
            else {
                cb("DB_ERROR");
            }
        }
        else {
            cb("FB_ERROR");
        }
    }

    

    checkIfExpertTakenRecentSession(dataitems) {
        //traceLog("fbconfigdata", _LastTimestampExpertsList);
        //traceLog("expertlist", dataitems);
        //_LastTimestampExpertsList[4509] = getServerUTCTimestamp()+1000;
        if (_LastTimestampExpertsList != 'undefined' && _LastTimestampExpertsList != null && typeof _LastTimestampExpertsList === "object" && dataitems != null) {
         
            let  _updatedCurrentUTCTimeStampForList = getServerUTCTimestampForList();
            
            if (_updatedCurrentUTCTimeStampForList > 0) {
                var finaldataitems = dataitems.filter(e => {
                    if (!_LastTimestampExpertsList.hasOwnProperty(e.PsychicId)) {
                        _LastTimestampExpertsList[e.PsychicId] = 0;
                    }

                    return (e.freemineligible == 1 && _updatedCurrentUTCTimeStampForList > _LastTimestampExpertsList[e.PsychicId] )
                });
                return finaldataitems;
            }
        }
        return dataitems;
    }
}