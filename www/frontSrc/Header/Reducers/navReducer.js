import {
  REQUEST_NAV_ITEMS,
  REQUEST_NAV_ITEMS_SUCCESS,
  REQUEST_NAV_ITEMS_FAIL,
} from '../Actions/navActions'

export default function navReducer (
  state = {
    dataFetched: false,
    isFetching: false,
    error: null,
    refreshTime: 300000
  },action
) {
  switch (action.type) {
    case REQUEST_NAV_ITEMS:
      return {
        ...state,
        dataFetched: false,
        isFetching: true,
      }
    case REQUEST_NAV_ITEMS_SUCCESS:

      return {
        ...state,
        data: action.data,
        dataFetched: true,
        isFetching: false,
        error: false,
      }
    case REQUEST_NAV_ITEMS_FAIL:
      return {
        ...state,
        isFetching: false,
        dataFetched: false,
        error: true,
      }
    default:
      return state
  }
}