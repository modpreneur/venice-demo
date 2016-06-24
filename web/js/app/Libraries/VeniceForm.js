/**
 * Created by bures on 06/26/16.
 */
'use strict';

import _ from 'lodash';
import TrinityForm from 'trinity/components/TrinityForm';
import Debug from 'trinity/Debug';
import {messageService} from 'trinity/Services';

const defaultSettings = {
    button : { // Defines which classes add to active button in which state
        ready: 'trinity-form-ready',
        success: 'trinity-form-success',
        timeout: 'trinity-form-timeout trinity-form-error'
    },
    requestTimeout: Debug.isDev() ? 60000 : 10000
};

export default class VeniceForm extends TrinityForm {
    constructor(element, type, settings){
        super(element, type, _.defaultsDeep(settings || {}, defaultSettings));
        this.success(this.__onSuccess, this);
        this.error(this.__onError, this);

        if(Debug.isDev()){
            this.addListener('submit-data', (data)=>{
                console.log('JSON DATA:', data);
            });
        }
    }

    /**
     * Success Callback
     * @param response
     * @private
     */
    __onSuccess(response){
        messageService(response.body['message'],'success');
        if(Debug.isDev()){
            console.log('Success', response);
        }
    }

    /**
     * Error callback
     * @param error
     * @private
     */
    __onError(error){
        Debug.error('ERROR', error);
        Debug.error(error.response);
        if(error.timeout){
            messageService('Request Timed out.<br>Try Again i few seconds.', 'error');
            return;
        }

        // Parse error object
        let response = error.response,
            error_info = response.type.indexOf('json') !== -1 ? response.body.error : response.text;

        // If it is just string, there is no need to continue
        if(_.isString(error_info)){
            messageService(error_info, 'error');
            return;
        }

        // More errors
        let noErrors = true;
        // DB errors
        if(error_info['db']){
            noErrors = false;
            // Special DB error ?
            let dbErrors = _.isString(error_info['db']) ? [error_info['db']] : error_info['db'];
            _.each(dbErrors, (err)=>{
                messageService(err, 'error');
            });

            let id = setTimeout(()=>{
                this.state = 'ready';
                this.unlock();
                clearTimeout(id);
            }, 2000);
        }

        // Global errors - CRFS token, etc.
        let globalErrors = error_info['global'];
        if(globalErrors && globalErrors.length !== 0){
            globalErrors = _.isString(globalErrors) ? [globalErrors] : globalErrors;
            noErrors = false;
            __globalErrors(globalErrors);
        }
        // Fields error
        if(error_info['fields'] && Object.keys(error_info['fields']).length > 0){
            noErrors = false;
            __fieldErrors.call(this, error_info['fields']);
        } else {
            this.unlock();
        }

        // Some error, but nothing related to Form
        if(noErrors && Debug.isDev()){
            messageService('DEBUG: Request failed but no FORM errors returned! check server response', 'warning');
        }
    }
}

/**
 * Export same types
 */
VeniceForm.formType = TrinityForm.formType;

/**
 * Handles global errors
 * @param errors
 * @private
 */
function __globalErrors(errors){
    let errLength = errors.length;
    for(let i=0; i< errLength; i++){
        if(errors[i].indexOf("The CSRF token is invalid") > -1) {
            messageService("Something get wrong, please refresh page.", 'warning');
        }else {
            messageService(errors[i], 'warning');
        }
    }
}

/**
 * Handles Field Errors - adds them to form
 * @param fields
 * @private
 */
function __fieldErrors(fields){
    let keys = Object.keys(fields),
        keysLength = keys.length;

    for(let i=0; i<keysLength; i++){
        let k = keys[i];
        this.addError(k, fields[k], document.getElementById(k));
    }
}
