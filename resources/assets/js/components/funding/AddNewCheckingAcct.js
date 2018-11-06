import React, {Component} from "react";
import Address from "../layout/controls/Address";
import EFTAccount from "../layout/controls/EFTAccount";
import Foundation from "foundation-sites";
import Toastr from "toastr";

Toastr.options.closeMethod = 'fadeOut';
Toastr.options.closeDuration = 300;
Toastr.options.closeEasing = 'swing';
Toastr.options.closeButton = true;
Toastr.options.preventDuplicates = true;
Toastr.options.progressBar = true;

class AddNewCheckingAcct extends Component {
	constructor(props) {
		super(props);
	}

	componentDidMount = () => {
		$(document).foundation();
	}

	handleAddAccount = (event) => {
		event.preventDefault();

		$('form#add-checking-form').foundation('validateForm');

		let $errorSpan = $("#form-acct-error");
		$errorSpan.text("");
	}

    render() {
		const styles = {
			hidden: {
				display: 'none'
			}
		}

        return (
			<div className="card animated fadeIn">
				<div className="card-divider">
					<h4>Add a new checking account</h4>
				</div>
				<div className="card-section">

					<form id="add-checking-form" data-abide noValidate>
						<div className="grid-container">

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12">
									<div data-abide-error className="alert callout" style={styles.hidden}>
										<p><i className="fi-alert"></i> There are some errors in your form.</p>
									</div>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-4">
									<label htmlFor="account-nickname">Account Nickname
										<input id="account-nickname" type="text" placeholder="account nickname"
											   aria-errormessage="numberError" required />
									</label>
									<span className="form-error" id="nickname-error" data-form-error-for="account-nickname">
										Please add a name to identify this account.
									</span>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<Address />
								<EFTAccount showDefault={true}/>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<span className="form-error" id="form-acct-error"></span>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<button id="add-checking-btn" className="button" onClick={this.handleAddAccount}>Add Account</button>
								</div>
							</div>
						</div>
					</form>

				</div>
			</div>
        );
    }
}

export default AddNewCheckingAcct;