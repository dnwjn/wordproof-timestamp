import React from 'react';
import ReactDOM from 'react-dom';
import Certificate from './components/Certficate/Certificate';

(function() {
  let schema = document.querySelector('.wordproof-schema');
  if (schema) {

    const res = phpBackwardsEncodingCompatibilityEncode(schema.innerHTML);
    let string = res.string;
    let entities = res.entities;
    schema = JSON.parse(string);
    console.log(schema);
    schema = phpBackwardsEncodingCompatibilityDecode(schema, entities);
    console.log(schema);
    if (document.querySelector('#wordproof-certificate-container')) {
      ReactDOM.render(<Certificate schema={schema} />, document.querySelector('#wordproof-certificate-container'));
      checkUrlForWordproof();
      addCertificateLinkEventListener();
      addCloseModalEventListener();
    }
  }
})();

function addCertificateLinkEventListener() {
  document.querySelector('.wordproof-certificate-helper').addEventListener('click', function () {
    showModal();
  }, false);
}

function addCloseModalEventListener() {
  let modal = getModal();
  modal.querySelector('.wordproof-modal-background').addEventListener('click', (e) => handleCloseModalEvent(e), false);
  modal.querySelector('.wordproof-modal-close').addEventListener('click', (e) => handleCloseModalEvent(e), false);
}

function handleCloseModalEvent(event) {
  event.preventDefault();
  hideModal();
}

/*
Show modal if the url contains #wordproof
 */
function checkUrlForWordproof() {
  if(window.location.href.indexOf("#wordproof") > -1) {
    showModal();
  }
}

function getModal() {
  return document.querySelector('#wordproof-certificate-container .shadowHost').shadowRoot.querySelector('.modal');
}

function hideModal() {
  getModal().classList.remove('is-active');
}

function showModal() {
  getModal().classList.add('is-active');
}

function phpBackwardsEncodingCompatibilityEncode(string) {
  let entities = [];
  string.replace(/\\u.{4}/g, function (entity) {
    let nice = JSON.parse('"' + entity + '"');
    entities.push(nice);
    return nice;
  });
  return {string: string, entities: entities};
}

function phpBackwardsEncodingCompatibilityDecode(object, entities) {
  let content = object.content;
  console.log(content);
  entities.forEach((entity) => {
    content.replace(entity, escapeUnicode(entity));
    console.log(entity, escapeUnicode(entity));
  });
  object.content = content;
  return object;
}

function escapeUnicode(str) {
  return str.replace(/[^\0-~]/g, function(ch) {
    return "\\u" + ("000" + ch.charCodeAt().toString(16)).slice(-4);
  });
}