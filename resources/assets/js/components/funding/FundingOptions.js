import React, {Component} from "react";
import Axios from "axios";
import _ from "underscore";

class FundingOptions extends Component {
	constructor(props) {
		super(props);

		this.state = { cardProfiles: [], eftProfiles: [] }
	}

	componentDidMount = () => {
		this.getFundingOptions();

		$('#funding-methods option:first').text('Loading...');
		$('#funding-methods').prop('disabled', true);
	}

	getFundingOptions = async () => {
		let data = JSON.parse(sessionStorage.getItem('playerData'));

		await Axios.post('/api/methods', {
			playerHash: data.player.playerhash,
		}).then(function(response) {
			this.setState({
				cardProfiles: response.data.card_profiles,
				eftProfiles: response.data.eft_profiles
			})
			$('#funding-methods option:first').text("--Select One--");
			$('#funding-methods').prop('disabled', false);
			$("#loader").hide();
		}.bind(this)).catch(function (error) {
			console.log(error);
		});
	}

    render() {
		console.log(this.state.cardProfiles);

		let cardMethods = _.map(this.state.cardProfiles, (method) => {
			if(this.state.cardProfiles.length > 0) {
				return <option key={method.id}
							   value="card"  id={"card-" + method.id}>
							{method.payment_method_nickname}: xxxx-xxxx-xxxx-{method.last_4_digits}
					</option>;
			}
			else {
				return <option key={1} disabled>No Credit/Debit cards</option>;
			}
		});

		let eftMethods = _.map(this.state.eftProfiles, (method) => {
			if(this.state.cardProfiles.length > 0) {
				return <option key={method.id}
							   value="card"  id={"eft-" + method.id}>
							{method.payment_method_nickname}: xxxx-xxxx-xxxx-{method.last_4_digits}
					</option>;
			}
			else {
				return <option key={1} disabled>No EFT accounts</option>;
			}
		});

        return (
			<label>Select Funding Method <img id="loader" src="../../images/loaders/loader_black_15.gif" />
				<select id="funding-methods" onChange={this.props.handleSelection.bind(this)}>
					<option value="default">-- Select One --</option>
					{cardMethods}
					<option disabled>──────────</option>
					{eftMethods}
					<option disabled>──────────</option>
					<option value="new_debit">Add new debit or credit card</option>
					<option value="new_checking">Add new checking account</option>
				</select>
			</label>
        );
    }
}

export default FundingOptions;