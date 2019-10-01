import React, {Component} from 'react'
import ModeDisplay from "../Extras/ModeDisplay";

export default class Automatic extends Component {
    constructor(props) {
        super(props);
        this.state = {
            wsfyIsActive: wordproofSettings.isWSFYActive,
            siteToken: wordproofSettings.wsfy.site_token,
            siteId: wordproofSettings.wsfy.site_id,
            showRevisions: wordproofSettings.wsfy.show_revisions,
            allowedPostTypes: wordproofSettings.wsfy.allowed_post_types,
            hideAdvanced: true,
            registeredPostTypes: Object.values(wordproofSettings.registeredPostTypes),
        }
    }

    handleAdvancedOptions = (e) => {
        e.preventDefault();
        this.setState({hideAdvanced: false});
    }

    render() {
        return (
            <div>
                <div className="vo-card vo-columns">
                    <div className="vo-col">
                        <h3>Automatic Settings</h3> <ModeDisplay active={(wordproofSettings.isWSFYActive) ? 'automatic' : 'manual'}/>
                        <p>Blockchain is not an easy to use technology. To timestamp content, you need your own
                            blockchain account, a configured wallet and the time to manually sign the timestamps. After
                            talking with hundreds of interested users, we developed an automated timestamping service.
                        </p>
                        <p>The same WordProof Timestamp, conform the <a
                            target="_blank" rel="noopener noreferrer" href="https://github.com/wordproof/timestamp-standard"
                          >open source protocol</a>, but then automatically on the background.</p>

                        <a className="button is-primary" href={wordproofSettings.urls.wizardConnect}>
                            Set your Site Key
                        </a>

                        <hr/>

                        <div className="form-group">
                            <label htmlFor="wsfy_settings[show_revisions]" className="label" title="Display Revisions">Show
                                Revisions</label>
                            <input type="checkbox" value="1" className="" name="wsfy_settings[show_revisions]"
                                   id="wsfy_settings[show_revisions]"
                                   onChange={e => this.setState({showRevisions: e.target.value})}
                                   defaultChecked={this.state.showRevisions}/>
                        </div>

                        <div className={`form-group ${this.state.hideAdvanced ? 'hidden' : ''}`}>
                            <label htmlFor="" className="label">Post Types to timestamp automatically</label>

                            {this.state.registeredPostTypes.map((value) => {
                                return <div key={value}>
                                    <input key={value} type="checkbox" value={value}
                                           name={`wsfy_settings[allowed_post_types][${value}]`}
                                           id={`wsfy_settings[allowed_post_types][${value}]`}
                                           defaultChecked={this.state.allowedPostTypes.includes(value)}/>
                                    <label htmlFor={`wsfy_settings[allowed_post_types][${value}]`}>{value}</label>
                                </div>
                            })}
                        </div>

                        {/*<div className={`form-group ${this.state.hideAdvanced ? 'hidden' : ''}`}>*/}
                        {/*    <label htmlFor="" className="label">Tools</label>*/}
                        {/*    <a href={`${wordproofSettings.adminUrl}admin.php?page=wordproof-autostamp`} target="_blank"*/}
                        {/*       rel="noopener noreferrer">Auto Stamp your Posts</a>*/}
                        {/*</div>*/}

                        <input type="submit" name="submit" id="submit" className="button is-primary"
                               value={wordproofSettings.saveChanges}/>

                        <button className={`button button-modest ${this.state.hideAdvanced ? '' : 'hidden'}`}
                                onClick={this.handleAdvancedOptions}>Show advanced settings
                        </button>

                    </div>
                    <div className="vo-col">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/of0PlfqV5EM" frameBorder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowFullScreen></iframe>
                    </div>
                </div>
            </div>
        )
    }
}