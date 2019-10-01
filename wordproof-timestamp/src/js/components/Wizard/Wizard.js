import React, {Component} from 'react'
import axios from 'axios';
import qs from 'qs';
import StepWizard from 'react-step-wizard';

import './Wizard.scss';
import Step1 from "./Steps/Step1";
import Step2 from "./Steps/Step2";
import Step3 from "./Steps/Step3";
import Step4 from "./Steps/Step4";
import Step5 from "./Steps/Step5";
import Nav from "./Nav";

export default class Wizard extends Component {

  constructor(props) {
    super(props);
    this.transitions = {};
    this.state = {
      fields: {...wordproof.currentValues}
    }
    console.log(this.state.fields);
  }

  updateField(e, slug = null, value = null) {
    if (e) {
      slug = e.target.getAttribute('data-slug');
      value = e.target.value;
    }

    let fields = this.state.fields;
    fields[slug] = value;
    this.setState(fields);
    this.updateRequest(slug, value);
  }

  updateRequest(slug, value) {
    axios.post(wordproof.updateSettingsEndpoint, qs.stringify({
      'action': 'wordproof_update_setting',
      'key': slug,
      'value': value
    }));
  }

  getField(slug) {
    return this.state.fields[slug];
  }

  render() {
    return (
      <div className="wordproof-onboarding-wizard bg-gray-300">
        <a href={wordproof.closeWizard} className={`absolute top-0 right-0 p-1 mx-4 my-2`}>Close Wizard</a>
        <div className={`flex flew-row justify-center items-center`}>
          <img className={`border-none h-12 mr-4`} src={wordproof.imagesUri + '/wordproof-icon-large.png'} />
          <img className={`border-none h-10`} src={wordproof.imagesUri + '/wordproof-logo.png'} />
        </div>
          <StepWizard transitions={this.transitions} isHashEnabled={true} nav={<Nav/>}>
            <Step1 hashKey={'mode'} />
            <Step2 hashKey={'connect'} get={this.getField.bind(this)} update={this.updateField.bind(this)} initial={wordproof.currentValues} />
            <Step3 hashKey={'customize'} get={this.getField.bind(this)} update={this.updateField.bind(this)} initial={wordproof.currentValues} />
            <Step4 hashKey={'certificate'} get={this.getField.bind(this)} update={this.updateField.bind(this)} initial={wordproof.currentValues} />
            <Step5 hashKey={'thanks'} get={this.getField.bind(this)} update={this.updateField.bind(this)} initial={wordproof.currentValues} />
          </StepWizard>
      </div>
    );
  }
}