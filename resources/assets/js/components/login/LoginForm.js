import "babel-polyfill";
import React, {Component} from "react";
import {faSignInAlt, faTimesCircle} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

class LoginForm extends Component {
	constructor(props) {
		super(props);

		this.state ={loggedIn: this.props.auth, errorMessage: ""}
	}

    render() {
        return (
			<div className="login animated fadeIn">
				<form action="/api/funding/login" method="post" onSubmit={this.props.handleSubmit.bind(this)}>
					<div id="login-form" className="card">
						<div className="card-divider">
							Login
						</div>
						<div className="card-section">
							<div style={{textAlign: "center"}}>
								<span className="error-msg"><small>{this.props.errorMessage}</small></span>
							</div>
							<label>Email Address
								<input type="text" name="email" placeholder="Email" defaultValue="larry.morris@scientificgames.com" />
							</label>

							<label>Registrar ID
								<input type="text" name="registrar_id" placeholder="ID" />
							</label>

                            <label>Password
                                <input type="password" name="password" placeholder="Password" />
                            </label>
                            <div className="grid-x grid-margin-x">
								<div className="cell small-12 text-center">
									<button type="submit" id="login-btn" className="button small">
										Login <FontAwesomeIcon icon={faSignInAlt} />
									</button>&nbsp;
									<button type="reset" id="reset-btn" className="button small">
										Reset <FontAwesomeIcon icon={faTimesCircle} />
									</button>
								</div>
                            </div>
						</div>
					</div>
				</form>
			</div>
        );
    }
}

export default LoginForm;