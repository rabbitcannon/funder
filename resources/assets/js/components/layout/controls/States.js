import React, {Component} from "react";
import _ from "underscore";

import StateList from "../../../config/states.json";


class States extends Component {
    render() {
		let states = _.map(StateList.states, (state, index) => {
			return <option key={index} value={state.abbreviation}>{state.name}</option>
		});

        return (
			<label>State
				<select id="state" required>
					<option value=""></option>
					{states}
				</select>
			</label>
        );
    }
}

export default States;