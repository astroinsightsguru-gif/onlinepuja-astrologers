class ExpertService {

    constructor() {
        this.config = {
            COUNTRY_CODE: "IN",
            EXPERT_LIST_URL_2: gWebsitePrefix + "psychics/ExpertList"
        };
        this.responseItems = {
            statusCode: 0,
            items: []
        };
    }

    async GetCountryCode() {
        return this.config.COUNTRY_CODE;
    }

    async GetExpertAsync(data) {
        return fetch(this.config.EXPERT_LIST_URL_2, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        }).then(response => {
                if (!response.ok) {
                    this.handleResponseError(401);
                }
                var responseJson = response.json();
                return responseJson;
            }).then(result => {
                //console.log("Retrieved items: GetExpertAsync",result);
                    return result;
                }).catch(error => {
                    this.handleResponseError(401);
                    this.handleError(error);
                })
    }

    async GetExpertLists(data) {
        return fetch(this.config.EXPERT_LIST_URL + "?" + new URLSearchParams(data))
            .then(response => {
                if (!response.ok) {
                    this.handleResponseError(401);
                }
                var responseJson = response.json();
                return responseJson;
            })
            .then(result => {
                traceLog("Retrieved items:",result);
                return result;
            })
            .catch(error => {
                this.handleResponseError(401);
                this.handleError(error);
            });
        //return Promise.resolve(this.items);
    }

    async getItem(itemLink) {
        return null;
    }

    async createItem(item) {
        traceLog("ItemService.createItem():", item);
        return Promise.resolve(item);
    }

    async deleteItem(itemId) {
        traceLog("ItemService.deleteItem(): item ID:" , itemId);
    }

    async updateItem(item) {
        traceLog("ItemService.updateItem():", item);
    }
    async handleResponseError(errorcode) {
        traceLog(errorcode);
    }
    async handleError(error) {
        traceLog(error.message);
    }
    
}
