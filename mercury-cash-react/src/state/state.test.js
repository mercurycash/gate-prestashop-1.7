import { renderHook, act } from '@testing-library/react-hooks'
import { initialState, store } from "./state";

test('state initData', async () => {
  let initData, state;
  renderHook(() => ({
    initData, state
  } = store()))
  expect(state).toBe(initialState)

  act(() => {
    initData({ 222: 123 })
  })
  expect(state).toStrictEqual({ ...initialState, initializedData: { 222: 123 } })
})

test('state changeStep', async () => {
  let changeStep, state;
  renderHook(() => ({
    changeStep, state
  } = store()))
  expect(state).toBe(initialState)

  act(() => {
    changeStep(2)
  })

  expect(state).toStrictEqual({ ...initialState, step: 2 })
})

test('state createTransactionStatus', async () => {
  let createTransactionStatus, state;
  renderHook(() => ({
    createTransactionStatus, state
  } = store()))
  expect(state).toBe(initialState)

  act(() => {
    createTransactionStatus(true)
  })

  expect(state).toStrictEqual({ ...initialState, createTransactionStatus: true })
})

test('state writeTransactionData', async () => {
  let writeTransactionData, state;
  renderHook(() => ({
    writeTransactionData, state
  } = store()))
  expect(state).toBe(initialState)

  act(() => {
    writeTransactionData({ some: 'object' })
  })

  expect(state).toStrictEqual({ ...initialState, transactionData: { some: 'object' } })
})

test('state updateStatusConfirmation', async () => {
  let updateStatusConfirmation, state;
  renderHook(() => ({
    updateStatusConfirmation, state
  } = store()))
  expect(state).toBe(initialState)

  act(() => {
    updateStatusConfirmation(2)
  })

  expect(state).toStrictEqual({
    ...initialState,
    transactionData: { ...initialState.transactionData, confirmations: 2 }
  })
})