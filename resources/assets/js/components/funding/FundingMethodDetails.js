import React, {Component} from "react";
import Toastr from "toastr";
import Axios from "axios";
import Foundation from "foundation-sites";

import FundAmount from "../layout/controls/FundAmount";
import FundingBlock from "./FundingBlock";

Toastr.options.closeMethod = 'fadeOut';
Toastr.options.closeDuration = 300;
Toastr.options.closeEasing = 'swing';
Toastr.options.closeButton = true;
Toastr.options.preventDuplicates = true;
Toastr.options.progressBar = true;

class FundingMethodDetails extends Component {
    constructor(props) {
        super(props);

        this.state = {
        	additionalAmount: 0,
            currentMethod: this.props.existingMethod,
			newAmount: 0
        }
    }

    componentDidMount = () => {
		$(document).foundation();
	}

	handleAmountChange = (event) => {
		let newFundsPence = event.target.value * 100;
		let currency = parseInt(event.target.value).toFixed(2);
		let newBalance = newFundsPence + this.props.balance;

		this.setState({
			additionalAmount: currency, newAmount: newBalance
		});
	}

	handlePayment = (event) => {
		event.preventDefault();

		$('form#add-funds-form').foundation('validateForm');
		$('#add-funds-btn').html('<img src="../../images/loaders/loader_pink_15_sharp.svg" /> Adding funds');

		let data = JSON.parse(sessionStorage.getItem('playerData'));
		let amount = parseInt($('#fund-amount').val()) * 100;
		let method_id = this.props.existingMethod.id;
		let type = this.props.paymentType;

		Axios.post('/api/funds/add', {
			playerHash: data.player.playerhash,
			existingMethod: true,
			amount: amount,
			method_id: method_id,
			funding_method_type: type,
			default: false,
		}).then(() => {
			$('form#add-funds-form').trigger("reset");
			$('#add-funds-btn').html('Add Funds');

			this.setState({
				additionalAmount: 0,
				newAmount: 0,
			});
			this.updateBalance();

			Toastr.success("Funding successful!");
		}).catch((error) => {
			$('#add-funds-btn').html('Add Funds');
			Toastr.error('Error: Unable to add funds.');
			console.log(error);
		});
	}

	updateBalance = () => {
		this.props.updateBalance();
	}

    render() {
        let paymentMethod = this.props.existingMethod;
		let displayComponent;

        if(paymentMethod != null || paymentMethod != undefined) {
        	displayComponent = <FundAmount handleAmountChange={this.handleAmountChange} />;
		}

        return (
			<div className="card animated fadeIn">
				<div className="card-divider">
					<h4>Existing Payment Method </h4>
				</div>
				<div className="card-section">

					<form id="add-funds-form" data-abide noValidate>
						<div className="grid-x grid-margin-x">
							<div className="cell medium-12 text-center">
								<h4>Method: {paymentMethod.payment_method_nickname}</h4>
							</div>
						</div>

						<div className="grid-x grid-margin-x">
							<div className="cell medium-12 text-center">
								<FundingBlock balance={this.props.balance} additionalAmount={this.state.additionalAmount} newAmount={this.state.newAmount}/>
							</div>
						</div>

						<div className="grid-x grid-margin-x">
							<div className="cell medium-offset-4 medium-4 text-center">
								{displayComponent}
							</div>
						</div>

						<div className="grid-x grid-margin-x">
							<div className="cell medium-12 text-center">
								<button id="add-funds-btn" className="button"
										onClick={(event) => this.handlePayment(event)}>Add Funds</button>
							</div>
						</div>
					</form>
                </div>
            </div>
        );
    }
}

export default FundingMethodDetails;