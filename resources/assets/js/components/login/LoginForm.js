import "babel-polyfill";
import React, {Component} from "react";

class LoginForm extends React.Component {
	constructor(props) {
		super(props);

		this.state ={loggedIn: this.props.auth, errorMessage: ""}
	}

    render() {
        return (
			<div className="login">
				<form action="/api/funding/login" method="post" onSubmit={this.props.handleSubmit.bind(this)}>
					<div className="card">
						<div className="card-divider">
							Login {this.props.auth}
						</div>
						<div className="card-section">
							<label>Email Address
								<input type="text" name="email" placeholder="Email" />
							</label>

							<label>Registrar ID
								<input type="text" name="registrar_id" placeholder="ID" />
							</label>

                            <label>Password
                                <input type="password" name="password" placeholder="Password" />
                            </label>
                            <div className="text-right">
                                <input type="submit" className="button" value="Login" />
                            </div>
						</div>
					</div>
				</form>
			</div>
        );
    }
}

export default LoginForm;