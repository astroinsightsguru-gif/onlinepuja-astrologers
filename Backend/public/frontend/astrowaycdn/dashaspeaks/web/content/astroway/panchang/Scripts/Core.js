var ASTROGURU = ASTROGURU || {};

//function panchang(response) {

//    try {
//        if (response) {
//            response = parseData(response.GetMonthPanchangForCDNResult);
//            callback(response);
//        }
//        else {
//            callback("");
//        }
//    } catch (e) {
//        clearInterval(loaderInterval);
//        $(".loaderDiv").hide();
//        $(".overlay").hide();
//    }
//}

ASTROGURU.core = (function () {
    "use strict";

    function ajax(apiName, data, callback) {

        $.ajax({
            url: apiName,
            type: 'GET',
            async: false,
            dataType: "json",
            success: function (response) {
                try {
                    if (response) {
                        response = parseData(response.GetMonthPanchangForCDNResult);
                        callback(response);
                    }
                    else {
                        callback("");
                    }
                } catch (e) {
                    //clearInterval(loaderInterval);
                    $(".loaderDiv").hide();
                    $(".overlay").hide();
                }
            },

            error: function (XMLHttpRequest, textStatus, errorThrown) {
                //console.log(textStatus);
                callback("");
            }

        });



        //var xhr = new XMLHttpRequest();
        //xhr.open("GET", apiName, true);
        //xhr.setRequestHeader("Content-type", "application/json");

        //xhr.onreadystatechange = function () {
        //    if (xhr.readyState == 4) {
        //        var response = xhr.responseText;
        //        try {
        //            if (response) {
        //                response = parseData(response);
        //                callback(response);
        //            }
        //            else {
        //                callback("");
        //            }
        //        } catch (e) {
        //            clearInterval(loaderInterval);
        //            $(".loaderDiv").hide();
        //            $(".overlay").hide();
        //        }
        //    }
        //};
        //xhr.onerror = function (error) {
        //    clearInterval(loaderInterval);
        //    $(".loaderDiv").hide();
        //    $(".overlay").hide();
        //};
        //xhr.send();

    };

    function ajaxGet(apiName, callback) {
        getAuthToken(function (response) {
            if (response != '') {
                $.ajax({
                    url: apiName,
                    type: 'GET',
                    crossDomain: true,
                    async: false,
                    dataType: "json",
                    beforeSend: function (request) {
                        request.setRequestHeader("AstroAuthToken", response);
                    },
                    success: function (response) {
                        try {
                            if (response) {
                                callback(response);
                            }
                            else {
                                callback("");
                            }
                        } catch (e) {
                            callback("");
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        //console.log(textStatus);
                        callback("");
                    }
                });
            }

        });


    };

    function ajaxPostCall(apiName, data, callback, accessTocken) {
        getAuthToken(function (response) {
            if (response != '') {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", apiName, true);
                xhr.setRequestHeader("Content-type", "application/json");
                if (typeof accessTocken != 'undefined' && accessTocken != null) {
                    xhr.setRequestHeader("Authorization", "Bearer " + accessTocken);
                }
                xhr.setRequestHeader("AstroAuthToken", response);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4) {
                        var response = xhr.responseText;
                        try {
                            if (response) {
                                response = JSON.parse(response);
                                callback(response);
                            }
                            else {
                                callback("");
                            }
                        } catch (e) {
                            //clearInterval(loaderInterval);
                            $(".loaderDiv").hide();
                            $(".overlay").hide();
                        }
                    }
                };
                xhr.onerror = function (error) {
                    //clearInterval(loaderInterval);
                    $(".loaderDiv").hide();
                    $(".overlay").hide();
                    callback("");
                };
                xhr.send(JSON.stringify(data));
            }
        });
    };

    function ajaxPost(apiName, data, callback) {
        getAuthToken(function (response) {
            if (response != '') {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", apiName, true);
                xhr.setRequestHeader("Content-type", "application/json");
                xhr.setRequestHeader("AstroAuthToken", response);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4) {
                        var response = xhr.responseText;
                        try {
                            if (response) {
                                response = JSON.parse(response);
                                response = parseData(response.RetTithi);
                                callback(response);
                            }
                            else {
                                callback("");
                            }
                        } catch (e) {
                            //clearInterval(loaderInterval);
                            $(".loaderDiv").hide();
                            $(".overlay").hide();
                        }
                    }
                };
                xhr.onerror = function (error) {
                    //clearInterval(loaderInterval);
                    $(".loaderDiv").hide();
                    $(".overlay").hide();
                    callback("");
                };
                xhr.send(JSON.stringify(data));
            }
        });
    };

    function parseData(response) {
        try {
            var newResponse = [];
            $(response.RV.CAL).each(function (i, v) {

                //var data = {
                //    AM: '', AMN: '', AMG: [], AMT: [], DK: [], FK: '', FKN: '', FN: [], GD: 0, GK: [], HD: '', MR: 0, MS: 0, MSN: '', NK: '', PK: '', PM: '', PMN: ''
                //        , RK: [], SK: '', SKAN: '', SR: 0, SS: 0, SSN: '', SSV: '', TH: '', THN: '', TID: '', VK: [], YG: '', YGK: [], STH: '', SYG: '', SNK: ''
                //        , SKN: '', DW: '', GRO: '', NKS: '', MSNN: '', SSNN: '', YGN: '', STHN: '', SYGN: '', SNKN: '', SKNN: '', SSVY: '', SSVN: ''
                //}



                //data.STHN = v.STHN;
                //data.SYGN = v.SYGN;
                //data.SNKN = v.SNKN;
                //data.SKNN = v.SKNN;
                //data.SSVN = v.SSVN;
                //data.SSVY = v.SSVY;

                //data.SSN = v.SSN;
                //data.SYG = v.SYG;
                ////data.DW
                //data.SKN = v.SKN;
                //data.SNK = v.SNK;
                //data.STH = v.STH;
                //data.YG = v.YG;
                //data.TID = v.TID;
                //data.SSV = v.SSVY + ' ' + v.SSVN; // v.SSV;
                //data.TH = v.TH;
                //data.THN = v.THN;
                //data.AM = v.AM;
                //data.AMN = v.AMN;
                //data.FK = v.FK;
                //data.FKN = v.FKN
                //data.FN = v.FN;
                //data.GD = v.GD;
                //data.HD = v.HD;
                //data.MSN = v.MSN;
                //data.NK = v.NK;
                //data.PK = v.PK;
                //data.PM = v.PM;
                //data.PMN = v.PMN;
                //data.SK = v.SK;
                //data.SKAN = v.SKAN;
                //data.SSN = v.SSN;
                //data.NKS = v.NKS;
                //data.MSNN = v.MSNN;
                //data.SSNN = v.SSNN;
                //data.YGN = v.YGN;


                //data.RK = typeof v.RK == 'undefined' ? null : typeof v.RK.ST == 'undefined' ? null : { SST: v.RK.SST, SET: v.RK.SET };
                //data.GK = typeof v.GK == 'undefined' ? null : typeof v.GK.ST == 'undefined' ? null : { SST: v.GK.SST, SET: v.GK.SET };
                //data.YGK = typeof v.YGK == 'undefined' ? null : typeof v.YGK.ST == 'undefined' ? null : { SST: v.YGK.SST, SET: v.YGK.SET };
                //data.AMT = typeof v.AMT == 'undefined' ? null : (typeof v.AMT.ST == 'undefined' || v.AMT.ST == 0) ? null : { SST: v.AMT.SST, SET: v.AMT.SET };

                //var varjyam = [];
                //$(v.VK).each(function (a, vvk) {
                //    varjyam.push({ SST: convertDecimalHoursToString(vvk.ST), SET: convertDecimalHoursToString(vvk.ET) })
                //});
                //data.VK = varjyam.length == 0 ? null : varjyam;

                //var amritGadiya = [];
                //$(v.AMG).each(function (a, vamg) {
                //    amritGadiya.push({ SST: convertDecimalHoursToString(vamg.ST), SET: convertDecimalHoursToString(vamg.ET) })
                //});
                //data.AMG = amritGadiya.length == 0 ? null : amritGadiya;

                //var Durmuhurt = [];
                //$(v.DK).each(function (a, vdk) {
                //    Durmuhurt.push({ SST: convertDecimalHoursToString(vdk.ST), SET: convertDecimalHoursToString(vdk.ET) })
                //});
                //data.DK = Durmuhurt.length == 0 ? null : Durmuhurt;

                //data.GRO = getDateString(v.GD);

                //data.SR = typeof v.SR == 'undefined' ? null : decimalTimeToString(v.SR, data.GRO);
                //data.SS = typeof v.SS == 'undefined' ? null : decimalTimeToString(v.SS, data.GRO);
                //data.MR = typeof v.MR == 'undefined' ? 'No Moon Rise' : decimalTimeToString(v.MR, data.GRO);
                //data.MS = typeof v.MS == 'undefined' ? null : decimalTimeToString(v.MS, data.GRO);

                //data.DW = getWeekDay(v.GD)



                //newResponse.push(data);
                newResponse.push(v);
            });
            response.RV.CAL = newResponse;
            return response;
        } catch (e) {

        }
    }

    function convertDecimalHoursToString(decimalTimeString) {
        if (parseFloat(decimalTimeString) == 0)
            return 0;
        //return (function (i) { return i + (Math.round(((time - i) * 60), 10) / 100); })(parseInt(time, 10));

        var decimalTime = parseFloat(decimalTimeString);
        decimalTime = decimalTime * 60 * 60;
        var hours = Math.floor((decimalTime / (60 * 60)));
        decimalTime = decimalTime - (hours * 60 * 60);
        var minutes = Math.floor((decimalTime / 60));
        decimalTime = decimalTime - (minutes * 60);
        var seconds = Math.round(decimalTime);
        if (hours < 10) {
            hours = "0" + hours;
        }
        if (minutes < 10) {
            minutes = "0" + minutes;
        }

        return ("" + hours + ":" + minutes);
    }

    function decimalTimeToString(decimalTime, currentDate) {
        var ticks = decimalTime
        var ticksToMicrotime = ticks / 10000;
        var epochMicrotimeDiff = Math.abs(new Date(0, 0, 1).setFullYear(1));
        var tickDate = new Date(ticksToMicrotime - epochMicrotimeDiff);

        var hours = tickDate.getHours();
        var minutes = tickDate.getMinutes();

        if (parseInt(currentDate.split('-')[1]) != tickDate.getDate())
            hours = parseInt(hours) + 24;

        if (hours < 10) {
            hours = "0" + hours;
        }
        if (minutes < 10) {
            minutes = "0" + minutes;
        }



        return ("" + hours + ":" + minutes);
    }

    function getDateString(longDate) {

        var ticks = longDate
        var ticksToMicrotime = ticks / 10000;
        var epochMicrotimeDiff = Math.abs(new Date(0, 0, 1).setFullYear(1));
        var tickDate = new Date(ticksToMicrotime - epochMicrotimeDiff);
        var mnth = tickDate.getMonth() + 1;
        if (mnth < 10) {
            mnth = "0" + mnth;
        }
        var dt = tickDate.getDate();
        if (dt < 10) {
            dt = "0" + dt;
        }
        var yr = tickDate.getFullYear();
        if (yr < 10) {
            yr = "0" + yr;
        }
        return mnth + "-" + dt + "-" + yr;
    }

    function getWeekDay(longDate) {
        var weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        var ticks = longDate
        var ticksToMicrotime = ticks / 10000;
        var epochMicrotimeDiff = Math.abs(new Date(0, 0, 1).setFullYear(1));
        var tickDate = new Date(ticksToMicrotime - epochMicrotimeDiff);
        return weekday[tickDate.getDay()];
    }

    function ajaxPostCallHtml(apiName, data, callback, accessTocken) {
        getAuthToken(function (response) {
            if (response != '') {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", apiName, true);
                xhr.setRequestHeader("Content-type", "application/json");
                if (typeof accessTocken != 'undefined' && accessTocken != null) {
                    xhr.setRequestHeader("Authorization", "Bearer " + accessTocken);
                }
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4) {
                        var response = xhr.responseText;
                        try {
                            if (response) {
                                callback(response);
                            }
                            else {
                                callback("");
                            }
                        } catch (e) {
                            //clearInterval(loaderInterval);
                            $(".loaderDiv").hide();
                            $(".overlay").hide();
                        }
                    }
                };
                xhr.onerror = function (error) {
                    //clearInterval(loaderInterval);
                    $(".loaderDiv").hide();
                    $(".overlay").hide();
                    callback("");
                };
                xhr.send(JSON.stringify(data));
            }
        });
    };

    function getAuthToken(callback) {
        $.ajax({
            url: $("#websiteurl").val() + 'GetAuthToken',
            type: 'GET',
            async: false,
            dataType: "json",
            success: function (response) {
                try {
                    if (response) {
                        callback(response);
                    }
                    else {
                        callback("");
                    }
                } catch (e) {
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                //console.log(textStatus);
                callback("");
            }

        });
    }

    function ajaxPostCallAsync(apiName, data, callback, accessTocken) {
        getAuthToken(function (response) {
            if (response != '') {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", apiName, true);
                xhr.setRequestHeader("Content-type", "application/json");
                if (typeof accessTocken != 'undefined' && accessTocken != null) {
                    xhr.setRequestHeader("Authorization", "Bearer " + accessTocken);
                }
                xhr.setRequestHeader("AstroAuthToken", response);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4) {
                        var response = xhr.responseText;
                        try {
                            if (response) {
                                response = JSON.parse(response);
                                callback(response);
                            }
                            else {
                                callback("");
                            }
                        } catch (e) {
                            //clearInterval(loaderInterval);
                            $(".loaderDiv").hide();
                            $(".overlay").hide();
                        }
                    }
                };
                xhr.onerror = function (error) {
                    //clearInterval(loaderInterval);
                    $(".loaderDiv").hide();
                    $(".overlay").hide();
                    callback("");
                };
                xhr.send(JSON.stringify(data));
            }
        });
    };

    function ajaxGetAsync(apiName, callback) {
        getAuthToken(function (response) {
            if (response != '') {
                $.ajax({
                    url: apiName,
                    type: 'GET',
                    crossDomain: true,
                    async: true,
                    dataType: "json",
                    beforeSend: function (request) {
                        request.setRequestHeader("AstroAuthToken", response);
                    },
                    success: function (response) {
                        try {
                            if (response) {
                                callback(response);
                            }
                            else {
                                callback("");
                            }
                        } catch (e) {
                            callback("");
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        //console.log(textStatus);
                        callback("");
                    }
                });
            }

        });
    };

    function ajaxGetAsyncHtml(apiName, callback) {
        getAuthToken(function (response) {
            if (response != '') {
                $.ajax({
                    url: apiName,
                    type: 'GET',
                    crossDomain: true,
                    async: true,
                    dataType: "html",
                    beforeSend: function (request) {
                        request.setRequestHeader("AstroAuthToken", response);
                    },
                    success: function (response) {
                        try {
                            if (response) {
                                callback(response);
                            }
                            else {
                                callback("");
                            }
                        } catch (e) {
                            callback("");
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        //console.log(textStatus);
                        callback("");
                    }
                });
            }

        });
    };

    function ajax(apiName, data, callback) {

        $.ajax({
            url: apiName,
            type: 'GET',
            async: false,
            dataType: "json",
            success: function (response) {
                try {
                    if (response) {
                        response = parseData(response.GetMonthPanchangForCDNResult);
                        callback(response);
                    }
                    else {
                        callback("");
                    }
                } catch (e) {
                    //clearInterval(loaderInterval);
                    $(".loaderDiv").hide();
                    $(".overlay").hide();
                }
            },

            error: function (XMLHttpRequest, textStatus, errorThrown) {
                //console.log(textStatus);
                callback("");
            }

        });

    };

    function ajaxDashaAstrologer(apiName, callback) {


        var xhr = new XMLHttpRequest();
        xhr.open("GET", apiName, true);
        xhr.setRequestHeader("Content-type", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4) {
                var response = xhr.responseText;
                try {
                    if (response) {
                        callback(response);
                    }
                    else {
                        callback("");
                    }
                } catch (e) {
                    $(".loaderDiv").hide();
                    $(".overlay").hide();
                }
            }
        };
        xhr.onerror = function (error) {
            $(".loaderDiv").hide();
            $(".overlay").hide();
            callback("");
        };
        xhr.send();

    };

    function ajaxGetPsychicAsync(apiName, callback) {
        $.ajax({
            url: apiName,
            type: 'GET',
            crossDomain: true,
            async: true,
            dataType: "html",
            success: function (response) {
                try {
                    if (response) {
                        callback(response);
                    }
                    else {
                        callback("");
                    }
                } catch (e) {
                    callback("");
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                callback("");
            }
        });
    };

    return {
        ajax: ajax,
        ajaxPost: ajaxPost,
        ajaxPostCall: ajaxPostCall,
        ajaxGet: ajaxGet,
        ajaxPostCallHtml: ajaxPostCallHtml,
        ajaxPostCallAsync: ajaxPostCallAsync,
        ajaxGetAsync: ajaxGetAsync,
        ajaxGetAsyncHtml: ajaxGetAsyncHtml,
        ajaxDashaAstrologer: ajaxDashaAstrologer,
        ajaxGetPsychicAsync: ajaxGetPsychicAsync
    }
})();
