var INDXDBJS = INDXDBJS || {};

let dbName = "chatlistdb";
let TableName = "tblchat"
let autoKey = true;
let keyName = "id";
INDXDBJS.LSEDB = null;
INDXDBJS.g_indexeddb = false; 

INDXDBJS.init=function(_dbName,_tableName){
    dbName = _dbName;
    TableName = _tableName;
}

INDXDBJS.openDB = function (callback) {
    try {
        window.indexedDB = window.indexedDB || window.mozIndexedDB ||
            window.webkitIndexedDB || window.msIndexedDB;

        window.IDBTransaction = window.IDBTransaction ||
            window.webkitIDBTransaction || window.msIDBTransaction;
        window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange ||
            window.msIDBKeyRange

        var b = indexedDB.open(dbName,2);
        b.onerror = function () {
            indexedDB.deleteDatabase(dbName);
        };
        b.onsuccess = function (b) {
            INDXDBJS.g_indexeddb = !0;
            INDXDBJS.LSEDB = b.target.result; 
            if (callback) {
                callback.call();
            }
        };
        b.addEventListener("success", function (event) { });
        b.onupgradeneeded = function (b) {
              var a=  b.target.result.createObjectStore(TableName, { keyPath: keyName });
        };      
    } catch (error) {
        console.error(error);
    }
};

INDXDBJS.createTableOperations = function () {
    try {
        if (INDXDBJS.LSEDB == null) {
            INDXDBJS.openDB();
        }
    } catch (error) {
        console.log(error);  
    }
}; 
 
INDXDBJS.InsertData = function (data) {
    try {
        if (INDXDBJS.LSEDB == null) {
            INDXDBJS.openDB();
        }
        if (INDXDBJS.LSEDB != null) {
            var transaction = INDXDBJS.LSEDB.transaction([TableName], "readwrite");
            var objectStore = transaction.objectStore(TableName);
            objectStore.add(data);
        }
    } catch (error) {
        console.error(error);
    }
};
 
INDXDBJS.ReadAll = function (successcallback) {
    if (_gLocalChat != "") {
        successcallback(_gLocalChat);
    } else {
        if (INDXDBJS.LSEDB != null) {
            var objectStore = INDXDBJS.LSEDB.transaction(TableName).objectStore(TableName);
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

INDXDBJS.ClearAllData = function () {
    //debugger;
    if (INDXDBJS.LSEDB == null) {
        INDXDBJS.openDB();
    }
    if (INDXDBJS.LSEDB != null) {
        var transaction = INDXDBJS.LSEDB.transaction([TableName], "readwrite");
        var objectStore = transaction.objectStore(TableName);
        objectStore.clear();
    }
}


INDXDBJS.InsertOrUpdateData = function (data) {
    //console.log(data);
    try {
        if (INDXDBJS.LSEDB == null) {
            INDXDBJS.openDB();
        }
        if (INDXDBJS.LSEDB != null) {
            var transaction = INDXDBJS.LSEDB.transaction([TableName], "readwrite");
            var objectStore = transaction.objectStore(TableName);
            objectStore.put(data);
        }
    } catch (error) {
        console.error(error);
    }
}

INDXDBJS.ReadQuestions = function (successcallback) {
    //console.log(INDXDBJS.LSEDB);
    if (INDXDBJS.LSEDB == null) {
        let loadDatat = function () { loadData(successcallback); };
        INDXDBJS.openDB(loadDatat);
    }
    else   {
        loadData(successcallback);
    }
    //else {
    //    successcallback(null);
    //}
}

function loadData(successcallback) {
    var objectStore = INDXDBJS.LSEDB.transaction(TableName).objectStore(TableName);
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

INDXDBJS.Get = function (id, onSuccess) {
    if (INDXDBJS.LSEDB != null) {
        INDXDBJS.LSEDB.transaction(TableName).objectStore(TableName).get(id).onsuccess = function (e) {
            //console.log(e);
            onSuccess(e.target.result);
        };
    }
}