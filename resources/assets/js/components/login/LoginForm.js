import "babel-polyfill";
import React, {Component} from "react";
import ReactDOM from "react-dom";
import Axios from "axios";
import $ from "jquery";

export default class LoginForm extends React.Component {
	handleSubmit = (event) => {
		event.preventDefault();

		Axios.post('/api/funding/login', {
			email: $('[name="email"]').val(),
			password: $('[name="password"]').val(),
			registrar_id: $('[name="registrar_id"]').val()
		}).then(function (response) {
			console.log(response);
		}).catch(function (error) {
			console.log(error);
		});
	}

    render() {
        return (
			<div className="login">
				<form action="/api/funding/login" method="post" onSubmit={this.handleSubmit.bind(this)}>
					<div className="card">
						<div className="card-divider">
							Login
						</div>
						<div className="card-section">
							<label>Email Address
								<input type="text" name="email" placeholder="Email" />
							</label>

                            <input type="hidden" name="registrar_id" value="1" />

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

ReactDOM.render(<LoginForm />, document.getElementById("login-form"));