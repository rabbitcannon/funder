import React, {Component} from "react";
import Address from "../layout/controls/Address";
import EFTAccount from "../layout/controls/EFTAccount";
import Foundation from "foundation-sites";
import Toastr from "toastr";
import Axios from "axios";

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

		$('form#add-checking-form').on("forminvalid.zf.abide", function(ev,frm) {
			console.log("Form id "+ev.target.id+" is invalid");
		});
		$('form#add-checking-form').on("formvalid.zf.abide", function(ev,frm) {
			console.log("Form id "+ev.target.id+" is valid");
		});

		let data = JSON.parse(sessionStorage.getItem('playerData'));
		let defaultCheck = $('#make_default').is(':checked')
		let checkValue = null;

		if(defaultCheck) {
			checkValue = true;
		}
		else {
			checkValue = false;
		}

		Axios.post('/api/methods/add', {
			playerHash: data.player.playerhash,
			eft_profile: {
				bank_name: $('#bank-name').val(),
				bank_account_type: "checking",
				account_holder_name: $('#card-name').val(),
				account_number: $('#acct-number').val(),
				routing_number: $('#routing-number').val(),
			},
			payment_method_nickname: $('#account-nickname').val(),
			funding_method_type: "eft_profile",
			default: checkValue,
			billing_details: {
				address_nickname: null,
				address1: $('#address_1').val(),
				address2: $('#address_2').val(),
				city: $('#city').val(),
				state: $('#state').val(),
				country: 'US',
				zip: $('#zip').val(),
			}
		}).then((response) => {
			this.updatePaymentMethods();
			Toastr.success('Payment method saved.');
			$('form#add-card-form').trigger("reset");
			$('#add-card-btn').html('Add Card');
		}).catch((error) => {
			Toastr.error('Error saving payment method.');
			$('#add-card-btn').html('Add Card');
			console.log(error);
		});
	}

	updatePaymentMethods = async () => {
		await this.props.updatePaymentMethods();
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