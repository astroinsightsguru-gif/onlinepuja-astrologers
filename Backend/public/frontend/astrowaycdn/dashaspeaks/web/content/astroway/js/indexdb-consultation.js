var INDXDBJSCONSULT = INDXDBJSCONSULT || {};

let dbName1 = "chatlistdb";
let TableName1 = "tblchat"
let autoKey1 = true;
let keyName1 = "id";
INDXDBJSCONSULT.LSEDB = null;
INDXDBJSCONSULT.g_indexeddb = false; 

INDXDBJSCONSULT.init=function(_dbName,_tableName){
    dbName1 = _dbName;
    TableName1 = _tableName;
}

INDXDBJSCONSULT.openDB = function (callback) {
    try {
        window.indexedDB = window.indexedDB || window.mozIndexedDB ||
            window.webkitIndexedDB || window.msIndexedDB;

        window.IDBTransaction = window.IDBTransaction ||
            window.webkitIDBTransaction || window.msIDBTransaction;
        window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange ||
            window.msIDBKeyRange

        var b = indexedDB.open(dbName1,2);
        b.onerror = function () {
            indexedDB.deleteDatabase(dbName1);
        };
        b.onsuccess = function (b) {
            INDXDBJSCONSULT.g_indexeddb = !0;
            INDXDBJSCONSULT.LSEDB = b.target.result; 
            if (callback) {
                callback.call();
            }
        };
        b.addEventListener("success", function (event) { });
        b.onupgradeneeded = function (b) {
              var a=  b.target.result.createObjectStore(TableName1, { keyPath: keyName });
        };      
    } catch (error) {
        console.error(error);
    }
};

INDXDBJSCONSULT.createTableOperations = function () {
    try {
        if (INDXDBJSCONSULT.LSEDB == null) {
            INDXDBJSCONSULT.openDB();
        }
    } catch (error) {
        console.log(error);  
    }
}; 
 
INDXDBJSCONSULT.InsertData = function (data) {
    try {
        if (INDXDBJSCONSULT.LSEDB == null) {
            INDXDBJSCONSULT.openDB();
        }
        if (INDXDBJSCONSULT.LSEDB != null) {
            var transaction = INDXDBJSCONSULT.LSEDB.transaction([TableName1], "readwrite");
            var objectStore = transaction.objectStore(TableName1);
            objectStore.add(data);
        }
    } catch (error) {
        console.error(error);
    }
};
 
INDXDBJSCONSULT.ReadAll = function (successcallback) {
    if (_gLocalChat != "") {
        successcallback(_gLocalChat);
    } else {
        if (INDXDBJSCONSULT.LSEDB != null) {
            var objectStore = INDXDBJSCONSULT.LSEDB.transaction(TableName1).objectStore(TableName1);
            var msg = [];
            objectStore.openCursor().onsuccess = function (event) {
                var cursor = event.target.result;
                if (cursor) {
                    var msgObj = {
                        userId: cursor.value.userId,
                        fileName: cursor.value.fileName,
                        message: cursor.value.message,
                        fileUrl: cursor.value.fileUrl,
                        type: cursor.value.type,
                        timestamp: cursor.value.timestamp,
                        name: cursor.value.name
                    };
                    //messageOriginal: cursor.value.message,
                    //fileSize: cursor.value.fileSize,
                    msg.push(msgObj);
                    cursor.continue();
                }
                else {
                    successcallback(msg);
                }
            };
        }
        else {
            successcallback("");
        }
    }


     
}

INDXDBJSCONSULT.ClearAllData = function () {
    if (INDXDBJSCONSULT.LSEDB == null) {
        INDXDBJSCONSULT.openDB();
    }
    if (INDXDBJSCONSULT.LSEDB != null) {
        var transaction = INDXDBJSCONSULT.LSEDB.transaction([TableName1], "readwrite");
        var objectStore = transaction.objectStore(TableName1);
        objectStore.clear();
    }
}


INDXDBJSCONSULT.InsertOrUpdateData = function (data) {
    //console.log(data);
    try {
        if (INDXDBJSCONSULT.LSEDB == null) {
            INDXDBJSCONSULT.openDB();
        }
        if (INDXDBJSCONSULT.LSEDB != null) {
            var transaction = INDXDBJSCONSULT.LSEDB.transaction([TableName1], "readwrite");
            var objectStore = transaction.objectStore(TableName1);
            objectStore.put(data);
        }
    } catch (error) {
        console.error(error);
    }
}

INDXDBJSCONSULT.ReadQuestions = function (successcallback) {
    //console.log(INDXDBJSCONSULT.LSEDB);
    if (INDXDBJSCONSULT.LSEDB == null) {
        let loadDatat = function () { loadData(successcallback); };
        INDXDBJSCONSULT.openDB(loadDatat);
    }
    else   {
        loadData(successcallback);
    }
    //else {
    //    successcallback(null);
    //}
}

function loadData(successcallback) {
    var objectStore = INDXDBJSCONSULT.LSEDB.transaction(TableName1).objectStore(TableName1);
    var msg = [];
    objectStore.openCursor().onsuccess = function (event) {
        var cursor = event.target.result;
        if (cursor) {
            msg.push(cursor.value);
            cursor.continue();
        }
        else {
            successcallback(msg);
        }
    };
}

INDXDBJSCONSULT.Get = function (id, onSuccess) {
    if (INDXDBJSCONSULT.LSEDB != null) {
        INDXDBJSCONSULT.LSEDB.transaction(TableName1).objectStore(TableName1).get(id).onsuccess = function (e) {
            //console.log(e);
            onSuccess(e.target.result);
        };
    }
}