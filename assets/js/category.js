
import Tagify from '@yaireo/tagify';
var inputElm = document.querySelector('.form__group__tag__input')
var tagify = new Tagify(inputElm,{
    whitelist: [],
});
var controller;

if (inputElm !== null) {
    tagify.on('input', onInput)
}

function onInput( e ){
    var value = e.detail.value;
    tagify.settings.whitelist.length = 0; // reset the whitelist

// https://developer.mozilla.org/en-US/docs/Web/API/AbortController/abort
controller && controller.abort();
controller = new AbortController();

// show loading animation and hide the suggestions dropdown
tagify.loading(true).dropdown.hide.call(tagify)

fetch('/categories.json?value='+ value)
    .then(RES => RES.json())
    .then(function(whitelist){
    // update inwhitelist Array in-place
    var names = whitelist.map(function(item) {
        return item['name'];
    });
    tagify.settings.whitelist.splice(0, whitelist.length, ...names)
    tagify.loading(false).dropdown.show.call(tagify, value); // render the suggestions dropdown
    })
}